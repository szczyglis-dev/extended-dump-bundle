<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Internals;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Core\Security;
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;

/**
 * UserDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class UserDumper implements InternalDumperInterface
{
    const LABEL_USER = 'User';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var ResponseEvent
     */
    private $event;

    /**
     * @var array
     */
    private $config = [];

    /**
     * UserDumper constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param array $config
     * @param ResponseEvent $event
     */
    public function init(array $config, ResponseEvent $event)
    {
        $this->config = $config;
        $this->event = $event;
    }

    /**
     * @param int $type
     * @return array
     */
    public function dump(int $type = 0): array
    {
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

        return [
            $k => $user,
        ];
    }
}