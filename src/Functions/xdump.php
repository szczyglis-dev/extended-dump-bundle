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
    function xdump()
    {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        for ($i = 0; $i < $numargs; $i++) {
            Dumper::xdump($arg_list[$i], null, Dumper::CALLER_FUNC);
        }  
    }
}