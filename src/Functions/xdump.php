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
     */
    function xdump()
    {
        $c = func_num_args();
        $args = func_get_args();
        for ($i = 0; $i < $c; $i++) {
            Dumper::xdump($args[$i], null, Dumper::CALLER_FUNC);
        }
    }
}
