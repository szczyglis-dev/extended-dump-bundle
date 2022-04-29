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

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;

/**
 * ParametersDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class ParametersDumper implements InternalDumperInterface
{
    const LABEL_ITEMS = 'parameters';

    /**
     * @var RequestStack
     */
    private $parameters;

    /**
     * @var ResponseEvent
     */
    private $event;

    /**
     * @var array
     */
    private $config = [];

    /**
     * ParametersDumper constructor.
     * @param ParameterBagInterface $parameters
     */
    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param array $config
     * @param ResponseEvent $event
     * @return mixed|void
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
        return $this->prepareParameters();
    }

    /**
     * @return array
     */
    public function prepareParameters()
    {
        $params = $this->parameters->all();
        $c = count($params);
        ksort($params);

        $k = $c . ' ' . self::LABEL_ITEMS;
        return [
            $k => $params,
        ];
    }
}