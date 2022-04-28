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
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Szczyglis\ExtendedDumpBundle\Event\MultiDumpEvents;
use Szczyglis\ExtendedDumpBundle\Event\RenderEvent;

/**
 * SystemDump
 * 
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class SystemDump
{
    const LABEL_REQUEST_STACK = 'RequestStack';
    const LABEL_SESSION = 'Session';
    const LABEL_GET = '$_GET';
    const LABEL_POST = '$_POST';
    const LABEL_COOKIES = 'Cookies';
    const LABEL_SERVER = 'Server';
    const LABEL_USER = 'User';    
    const LABEL_PHP = 'PHP';
    const LABEL_PHP_EXTENSIONS = 'PHP-EXT';
    const LABEL_ENV = '$_ENV';
    const LABEL_SERVER_KEY = '$_SERVER';
    const LABEL_ITEMS = 'item(s)';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * SystemDump constructor.
     * @param RequestStack $requestStack
     * @param Security $security
     */
    public function __construct(RequestStack $requestStack, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * @return array
     */
    public function getVars(): array
    {
        $server = [];
        $server[self::LABEL_PHP] = phpversion();
        $server[self::LABEL_PHP_EXTENSIONS] = [];

        $extensions = get_loaded_extensions();
        foreach ($extensions as $extension) {
            $server[self::LABEL_PHP_EXTENSIONS][strtolower($extension)] = phpversion($extension);
        }
        ksort($server[self::LABEL_PHP_EXTENSIONS]);

        if (isset($_ENV) && !empty($_ENV)) {
            $server[self::LABEL_ENV] = $_ENV;
            ksort($server[self::LABEL_ENV]);
        }
        if (isset($_SERVER) && !empty($_SERVER)) {
            $server[self::LABEL_SERVER_KEY] = $_SERVER;
            ksort($server[self::LABEL_SERVER_KEY]);
        }
        return $server;
    }

    /**
     * @return void
     */
    public function dump()
    {        
        Dumper::xdump($this->requestStack, self::LABEL_REQUEST_STACK, Dumper::CALLER_SYSTEM);

        // Symfony versions difference fixes
        if (method_exists($this->requestStack, 'getSession')) {
            $data = $this->requestStack->getSession();
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_SESSION, Dumper::CALLER_SYSTEM);
        }        
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $data = $this->requestStack->getMainRequest()->query;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_GET, Dumper::CALLER_SYSTEM);

            $data = $this->requestStack->getMainRequest()->request;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_POST, Dumper::CALLER_SYSTEM);

            $data = $this->requestStack->getMainRequest()->cookies;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_COOKIES, Dumper::CALLER_SYSTEM);
        } else if (method_exists($this->requestStack, 'getMasterRequest')) {
            $data = $this->requestStack->getMasterRequest()->query;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_GET, Dumper::CALLER_SYSTEM);

            $data = $this->requestStack->getMasterRequest()->request;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_POST, Dumper::CALLER_SYSTEM);

            $data = $this->requestStack->getMasterRequest()->cookies;
            $k = $data->count().' '.self::LABEL_ITEMS;
            Dumper::xdump([$k => $data->all()], self::LABEL_COOKIES, Dumper::CALLER_SYSTEM);
        }        

        $server = $this->getVars();
        Dumper::xdump($server, self::LABEL_SERVER, Dumper::CALLER_SYSTEM);

        $k = self::LABEL_USER;
        $user = null;
        if (!is_null($this->security->getUser())) {
            $user = $this->security->getUser();
            if (method_exists($user, 'getUsername')) {
                $k = $user->getUsername();
            } elseif (method_exists($user, 'getUserIdentifier')) {
                $k = $user->getUserIdentifier();
            }
            if (empty($k)) {
                $k = self::LABEL_USER;
            }
        }
        Dumper::xdump([$k => $user], self::LABEL_USER, Dumper::CALLER_SYSTEM);
    }
}