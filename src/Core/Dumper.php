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

/**
 * Dumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Dumper
{
    /**
     * @var int Dump called by system/server vars
     */
    const CALLER_SYSTEM = -1;

    /**
     * @var int Dump called directly by Dumper::xdump
     */
    const CALLER_STATIC = 0;

    /**
     * @var int Dump called by function xdump
     */
    const CALLER_FUNC = 1;

    /**
     * @var int Dump called from Twig template
     */
    const CALLER_TWIG = 2;

    /**
     * @var int Dump called by Event
     */
    const CALLER_EVENT = 3;

    /**
     * @var string Key for app section
     */
    const KEY_APP = 'app';

    /**
     * @var string Key for events section
     */
    const KEY_EVENT = 'event';

    /**
     * @var string Key for system section
     */
    const KEY_SYSTEM = 'system';

    /**
     * @param mixed $var Variable to be dumped
     * @param string|null $label Optional label
     * @param int|null $caller Caller type
     */
    public static function xdump($var, ?string $label = null, ?int $caller = null)
    {
        if (is_null($caller)) {
            $caller = self::CALLER_STATIC;
        }

        if (is_object($var)) {
            $var = clone $var;
        }

        $item = new Item;
        $item->setVar($var);
        $item->setLabel($label);
        $item->setCaller($caller);
        $item->setTrace(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3));

        Trace::append($item);
        Registry::add($item);
    }
}