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
     * @var SystemDump
     */
    private $systemDump;

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
     * @param SystemDump $systemDump
     */
    public function __construct(array $config, Environment $twig, KernelInterface $kernel, EventDispatcherInterface $dispatcher, SystemDump $systemDump)
    {
        $this->config = $config;
        $this->twig = $twig;
        $this->kernel = $kernel;
        $this->dispatcher = $dispatcher;
        $this->systemDump = $systemDump;
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

    /**
     * @return string Rendered output
     * @throws \ReflectionException
     */
    public function generate()
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

        $this->systemDump->dump();

        $this->dispatchEvent();

        $items = [
            'app' => [],
            'event' => [],
            'system' => [],
        ];
        $vars = Registry::all();
        $count = Registry::count();        

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

            if (isset($this->config['display']['sections'][$key]['enabled']) 
                && $this->config['display']['sections'][$key]['enabled'] === false) {
                    continue;
            }
            
            $items[$key][] = $item;
        }

        if (!isset($this->config['display']['sections']['system']['collapsed'])) {
            $this->config['display']['sections']['system']['collapsed'] = true;
        }

        $hash = substr(md5(random_bytes(32)), 0, 5);

        return $this->twig->render('@ExtendedDump/_main.html.twig', [
            'items' => $items,
            'hash' => $hash,
            'count' => $count,
            'config' => $this->config,
        ]);
    }
}