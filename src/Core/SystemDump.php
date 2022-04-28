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
        $server['PHP'] = phpversion();
        $server['PHP-EXT'] = [];

        $extensions = get_loaded_extensions();
        foreach ($extensions as $extension) {
            $server['PHP-EXT'][strtolower($extension)] = phpversion($extension);
        }
        ksort($server['PHP-EXT']);

        if (isset($_ENV) && !empty($_ENV)) {
            $server['$_ENV'] = $_ENV;
            ksort($server['$_ENV']);
        }
        if (isset($_SERVER) && !empty($_SERVER)) {
            $server['$_SERVER'] = $_SERVER;
            ksort($server['$_SERVER']);
        }
        return $server;
    }

    /**
     * @return void
     */
    public function dump()
    {        
        Dumper::xdump($this->requestStack, 'RequestStack', Dumper::CALLER_SYSTEM);

        // Symfony versions difference fixes
        if (method_exists($this->requestStack, 'getSession')) {
            Dumper::xdump($this->requestStack->getSession()->all(), 'Session', Dumper::CALLER_SYSTEM);
        }        
        if (method_exists($this->requestStack, 'getMainRequest')) {
            Dumper::xdump($this->requestStack->getMainRequest()->request->all(), '$_POST', Dumper::CALLER_SYSTEM);
            Dumper::xdump($this->requestStack->getMainRequest()->cookies->all(), 'Cookies', Dumper::CALLER_SYSTEM);
        } else if (method_exists($this->requestStack, 'getMasterRequest')) {
            Dumper::xdump($this->requestStack->getMasterRequest()->request->all(), '$_POST', Dumper::CALLER_SYSTEM);
            Dumper::xdump($this->requestStack->getMasterRequest()->cookies->all(), 'Cookies', Dumper::CALLER_SYSTEM);
        }        

        $server = $this->getVars();
        Dumper::xdump($server, 'Server', Dumper::CALLER_SYSTEM);

        $k = 'user';
        $user = null;
        if (!is_null($this->security->getUser())) {
            $user = $this->security->getUser();
            if (method_exists($user, 'getUsername')) {
                $k = $user->getUsername();
            } elseif (method_exists($user, 'getUserIdentifier')) {
                $k = $user->getUserIdentifier();
            }
        }
        Dumper::xdump([$k => $user], 'User', Dumper::CALLER_SYSTEM);
    }
}