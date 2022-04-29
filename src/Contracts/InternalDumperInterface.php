<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Contracts;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * SystemDump
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
interface InternalDumperInterface
{
    /**
     * @param array $config
     * @param ResponseEvent $event
     * @return mixed
     */
    public function init(array $config, ResponseEvent $event);

    /**
     * @param int $type
     * @return array
     */
    public function dump(int $type = 0): array;
}