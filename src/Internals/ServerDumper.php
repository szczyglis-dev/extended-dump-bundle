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
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;

/**
 * ServerDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class ServerDumper implements InternalDumperInterface
{
    const LABEL_PHP = 'PHP';
    const LABEL_PHP_EXTENSIONS = 'PHP-EXT';
    const LABEL_ENV = '$_ENV';
    const LABEL_SERVER_KEY = '$_SERVER';
    /**
     * @var ResponseEvent
     */
    private $event;
    /**
     * @var array
     */
    private $config = [];

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
}