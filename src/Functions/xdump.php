<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Szczyglis\ExtendedDumpBundle\Core\Dumper;

if (!function_exists('xdump')) {
    /**
     * @param $var
     * @param string|null $label
     * @return mixed
     */
    function xdump($var, ?string $label = null)
    {
        Dumper::xdump($var, $label, Dumper::CALLER_FUNC);
        return $var;
    }
}