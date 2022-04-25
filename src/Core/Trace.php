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
 * Trace
 * 
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Trace
{
    /**
     * @param Item $item
     * @throws \ReflectionException
     */
    public static function append(Item $item)
    {
        $trace = $item->getTrace();
        $caller = $item->getCaller();

        if ($caller == Dumper::CALLER_SYSTEM || $caller == Dumper::CALLER_EVENT) {
            return;
        }

        $file = null;
        $classname = null;
        $line = null;
        $function = null;

        switch ($caller) {
            case Dumper::CALLER_STATIC:
                $i = 0;
                if (isset($trace[$i])) {
                    $file = basename($trace[$i]['file']);
                    $line = $trace[$i]['line'];
                }
                $i++;
                if (isset($trace[$i])) {
                    if (isset($trace[$i]['class'])) {
                        $classname = self::shortenClassName($trace[$i]['class']);
                    }
                    if (isset($trace[$i]['function'])) {
                        $function = $trace[$i]['function'];
                    }
                }
                break;

            case Dumper::CALLER_FUNC:
                $i = 0;
                if (isset($trace[1]['function']) && $trace[1]['function'] == 'xdump') {
                    $i++;
                }
                if (isset($trace[$i])) {
                    $file = basename($trace[$i]['file']);
                    $line = $trace[$i]['line'];
                }
                $i++;
                if (isset($trace[$i])) {
                    if (isset($trace[$i]['class'])) {
                        $classname = self::shortenClassName($trace[$i]['class']);
                    }
                    if (isset($trace[$i]['function'])) {
                        $function = $trace[$i]['function'];
                    }
                }
                break;

            case Dumper::CALLER_TWIG:
                $i = 0;
                if (isset($trace[1]['function']) && $trace[1]['function'] == 'xdump') {
                    $i++;
                }
                $i++;
                if (isset($trace[$i])) {
                    if (isset($trace[$i]['function']) && $trace[$i]['function'] != 'doDisplay') {
                        $function = preg_replace('/^block_/', 'block: ', $trace[$i]['function']);
                    }
                }
                break;
        }

        $item->setTrace($trace);
        $item->setFile($file);
        $item->setLine($line);
        $item->setFunction($function);
        $item->setClass($classname);
    }

    /**
     * @param string $name
     * @return string
     * @throws \ReflectionException
     */
    private static function shortenClassName(string $name): string
    {
        return (new \ReflectionClass($name))->getShortName();
    }
}