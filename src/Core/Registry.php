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
 * Registry
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Registry
{
    /**
     * @var array Array of vars to be dumped
     */
    private static $items = [];

    /**
     * @param Item $item
     */
    public static function add(Item $item)
    {
        self::$items[] = $item;
    }

    /**
     * @return array
     */
    public static function all()
    {
        return self::$items;
    }

    /**
     * @return int
     */
    public static function count()
    {
        $c = 0;
        foreach (self::$items as $item) {
            if ($item->getCaller() != Dumper::CALLER_SYSTEM && $item->getCaller() != Dumper::CALLER_EVENT) {
                $c++;
            }
        }
        return $c;
    }
}