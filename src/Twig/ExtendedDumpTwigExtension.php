<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Szczyglis\ExtendedDumpBundle\Core\Dumper;

/**
 * ExtendedDumpTwigExtension
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class ExtendedDumpTwigExtension extends AbstractExtension
{
    /**
     */
    public function xdump()
    {
        $c = func_num_args();
        $args = func_get_args();
        for ($i = 0; $i < $c; $i++) {
            Dumper::xdump($args[$i], null, Dumper::CALLER_TWIG);
        }
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('xdump', [$this, 'xdump']),
        ];
    }
}
