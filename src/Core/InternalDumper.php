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

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;
use Szczyglis\ExtendedDumpBundle\Internals\RequestDumper;
use Szczyglis\ExtendedDumpBundle\Internals\ServerDumper;
use Szczyglis\ExtendedDumpBundle\Internals\UserDumper;
use Szczyglis\ExtendedDumpBundle\Internals\DoctrineDumper;
use Szczyglis\ExtendedDumpBundle\Internals\ParametersDumper;

/**
 * InternalDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class InternalDumper
{
    const LABEL_REQUEST = 'Request / Response';
    const LABEL_SESSION = 'Session';
    const LABEL_GET = '$_GET';
    const LABEL_POST = '$_POST';
    const LABEL_COOKIES = 'Cookies';
    const LABEL_SERVER = 'Server';
    const LABEL_USER = 'User';
    const LABEL_DOCTRINE = 'Doctrine';
    const LABEL_PARAMETERS = 'Parameters';

    /**
     * @var array
     */
    private $dumpers = [];

    /**
     * InternalDumper constructor.
     * @param RequestDumper $requestDumper
     * @param ServerDumper $serverDumper
     * @param UserDumper $userDumper
     * @param DoctrineDumper $doctrineDumper
     * @param ParametersDumper $parametersDumper
     */
    public function __construct(RequestDumper $requestDumper,
                                ServerDumper $serverDumper,
                                UserDumper $userDumper,
                                DoctrineDumper $doctrineDumper,
                                ParametersDumper $parametersDumper)
    {
        $this->dumpers = [
            'request' => $requestDumper,
            'server' => $serverDumper,
            'user' => $userDumper,
            'doctrine' => $doctrineDumper,
            'parameters' => $parametersDumper,
        ];
    }

    /**
     * @param array $config
     * @param ResponseEvent $event
     * @return void
     */
    public function dump(array $config, ResponseEvent $event)
    {
        $this->init($config, $event);

        $display = [];
        $display['request'] = true;
        $display['response'] = true;
        $display['doctrine'] = true;
        $display['session'] = true;
        $display['get'] = true;
        $display['post'] = true;
        $display['cookies'] = true;
        $display['server'] = true;
        $display['user'] = true;
        $display['parameters'] = true;

        if (isset($config['display']['sections']['system']['items'])
            && is_array($config['display']['sections']['system']['items'])) {
            $cfg = $config['display']['sections']['system']['items'];
            foreach ($display as $k => $value) {
                if (isset($cfg[$k]) && $cfg[$k] !== true) {
                    $display[$k] = false;
                }
            }
        }

        if ($display['request'] || $display['response']) {
            $response = [];
            if ($display['request']) {
                $response = $this->get('request')->dump(RequestDumper::TYPE_REQUEST);
            }
            if ($display['response']) {
                $response += $this->get('request')->dump(RequestDumper::TYPE_RESPONSE);
            }
            Dumper::xdump($response, self::LABEL_REQUEST, Dumper::CALLER_SYSTEM);
        }
        if ($display['doctrine']) {
            Dumper::xdump($this->get('doctrine')->dump(), self::LABEL_DOCTRINE, Dumper::CALLER_SYSTEM);
        }
        if ($display['session']) {
            Dumper::xdump($this->get('request')->dump(RequestDumper::TYPE_SESSION), self::LABEL_SESSION, Dumper::CALLER_SYSTEM);
        }
        if ($display['get']) {
            Dumper::xdump($this->get('request')->dump(RequestDumper::TYPE_GET), self::LABEL_GET, Dumper::CALLER_SYSTEM);
        }
        if ($display['post']) {
            Dumper::xdump($this->get('request')->dump(RequestDumper::TYPE_POST), self::LABEL_POST, Dumper::CALLER_SYSTEM);
        }
        if ($display['cookies']) {
            Dumper::xdump($this->get('request')->dump(RequestDumper::TYPE_COOKIES), self::LABEL_COOKIES, Dumper::CALLER_SYSTEM);
        }
        if ($display['user']) {
            Dumper::xdump($this->get('user')->dump(), self::LABEL_USER, Dumper::CALLER_SYSTEM);
        }
        if ($display['server']) {
            Dumper::xdump($this->get('server')->dump(), self::LABEL_SERVER, Dumper::CALLER_SYSTEM);
        }
        if ($display['parameters']) {
            Dumper::xdump($this->get('parameters')->dump(), self::LABEL_PARAMETERS, Dumper::CALLER_SYSTEM);
        }
    }

    /**
     * @param array $config
     * @param ResponseEvent $event
     */
    public function init(array $config, ResponseEvent $event)
    {
        foreach ($this->dumpers as $dumper) {
            $dumper->init($config, $event);
        }
    }

    /**
     * @param string $name
     * @return InternalDumperInterface|null
     */
    public function get(string $name): ?InternalDumperInterface
    {
        if (isset($this->dumpers[$name]) && $this->dumpers[$name] instanceof InternalDumperInterface) {
            return $this->dumpers[$name];
        }
        return null;
    }
}