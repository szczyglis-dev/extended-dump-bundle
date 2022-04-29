<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Core;

use Twig\Environment;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Szczyglis\ExtendedDumpBundle\Event\MultiDumpEvents;
use Szczyglis\ExtendedDumpBundle\Event\RenderEvent;

/**
 * Generator
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Generator
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var InternalDumper
     */
    private $internalDumper;

    /**
     * @var array Configuration
     */
    private $config = [];

    /**
     * Generator constructor.
     * @param array $config
     * @param Environment $twig
     * @param KernelInterface $kernel
     * @param EventDispatcherInterface $dispatcher
     * @param InternalDumper $internalDumper
     */
    public function __construct(array $config,
                                Environment $twig,
                                KernelInterface $kernel,
                                EventDispatcherInterface $dispatcher,
                                InternalDumper $internalDumper)
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->kernel = $kernel;
        $this->dispatcher = $dispatcher;
        $this->internalDumper = $internalDumper;
    }

    /**
     * @param ResponseEvent $event
     * @return string Rendered output
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generate(ResponseEvent $event)
    {
        $env = $this->kernel->getEnvironment();
        if (isset($this->config['env']) && !empty($this->config['env'])) {
            if (!in_array($env, $this->config['env'])) {
                return false;
            }
        } else {
            if ($env != 'dev') {
                return false;
            }
        }

        if (isset($this->config['display']['enabled']) && $this->config['display']['enabled'] === false) {
            return false;
        }

        $display = [];
        $display['app'] = true;
        $display['event'] = true;
        $display['system'] = true;

        if (isset($this->config['display']['sections'])
            && is_array($this->config['display']['sections'])) {
            $cfg = $this->config['display']['sections'];
            foreach ($display as $k => $value) {
                if (isset($cfg[$k]['enabled']) && $cfg[$k]['enabled'] !== true) {
                    $display[$k] = false;
                }
            }
        }

        if (!$display['app'] && !$display['system'] && !$display['event']) {
            return false;
        }
        if ($display['system']) {
            $this->internalDumper->dump($this->config, $event);
        }
        if ($display['event']) {
            $this->dispatchEvent();
        }

        $items = [
            'app' => [],
            'event' => [],
            'system' => [],
        ];

        $options = [
            'max_items' => -1,
            'max_depth' => 1,
            'max_string_depth' => 160,
        ];

        $vars = Registry::all();
        $count = Registry::count();

        $dumper = new HtmlDumper();
        $cloner = new VarCloner();
        $cloner->setMaxItems($options['max_items']);

        foreach ($vars as $item) {
            $key = Dumper::KEY_APP;
            switch ($item->getCaller()) {
                case Dumper::CALLER_SYSTEM:
                    $key = Dumper::KEY_SYSTEM;
                    break;
                case Dumper::CALLER_EVENT:
                    $key = Dumper::KEY_EVENT;
                    break;
            }

            if (array_key_exists($key, $display) && $display[$key] === false) {
                continue;
            }

            $item->setDump($dumper->dump($cloner->cloneVar($item->getVar()), true, [
                'maxDepth' => $options['max_depth'],
                'maxStringLength' => $options['max_string_depth'],
            ]));

            $items[$key][] = $item;
        }

        $hash = substr(md5(random_bytes(32)), 0, 5);

        return $this->twig->render('@ExtendedDump/_main.html.twig', [
            'items' => $items,
            'hash' => $hash,
            'count' => $count,
            'config' => $this->config,
        ]);
    }

    public function dispatchEvent()
    {
        $event = new RenderEvent;
        $this->dispatcher->dispatch($event, RenderEvent::NAME);

        $data = $event->getData();
        foreach ($data as $item) {
            Dumper::xdump($item['var'], $item['label'], Dumper::CALLER_EVENT);
        }
    }
}