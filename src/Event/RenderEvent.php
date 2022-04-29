<?php

/**
 * This file is part of szczyglis/extended-dump-bundle.
 *
 * (c) Marcin Szczyglinski <szczyglis@protonmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Szczyglis\ExtendedDumpBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * RenderEvent
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class RenderEvent extends Event
{
    /**
     * @var string Event name
     */
    public const NAME = 'extended_dump.render';

    /**
     * @var array Array of vars to be dumped
     */
    private $data = [];

    /**
     * @param mixed $var Variable to be dumped
     * @param string|null $label Optional label
     */
    public function add($var, ?string $label = null)
    {
        $this->data[] = [
            'var' => $var,
            'label' => $label,
        ];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}