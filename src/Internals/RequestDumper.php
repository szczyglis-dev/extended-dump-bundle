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

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Szczyglis\ExtendedDumpBundle\Contracts\InternalDumperInterface;

/**
 * RequestDumper
 *
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class RequestDumper implements InternalDumperInterface
{
    const TYPE_REQUEST = 1;
    const TYPE_RESPONSE = 2;
    const TYPE_SESSION = 3;
    const TYPE_GET = 4;
    const TYPE_POST = 5;
    const TYPE_COOKIES = 6;

    const LABEL_ITEMS = 'item(s)';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var ResponseEvent
     */
    private $event;

    /**
     * @var array
     */
    private $config = [];

    /**
     * RequestDumper constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
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
        switch ($type) {
            case self::TYPE_REQUEST:
                return $this->prepareRequest();
                break;

            case self::TYPE_RESPONSE:
                return $this->prepareResponse();
                break;

            case self::TYPE_SESSION:
                return $this->prepareSession();
                break;

            case self::TYPE_GET:
                return $this->prepareGet();
                break;

            case self::TYPE_POST:
                return $this->preparePost();
                break;

            case self::TYPE_COOKIES:
                return $this->prepareCookies();
                break;

            default:
                return [];
        }
    }

    /**
     * @return array
     */
    public function prepareRequest()
    {
        return [
            'request' => $this->requestStack,
        ];
    }

    /**
     * @return array
     */
    public function prepareResponse()
    {
        if (is_null($this->event)) {
            return [];
        }

        return [
            'response' => $this->event->getResponse(),
        ];
    }

    /**
     * @return array
     */
    public function prepareSession()
    {
        $result = [];

        try {
            // Symfony 5.4+
            if (method_exists($this->requestStack, 'getSession')) {
                $data = $this->requestStack->getSession();
                if (!is_null($data)) {
                    $k = $data->count() . ' ' . self::LABEL_ITEMS;
                    $result = [
                        $k => $data->all(),
                    ];
                }
            }
        } catch (\Throwable $e) {
            //
        } 
        
        return $result;
    }

    /**
     * @return array
     */
    public function prepareGet()
    {
        $result = [];

        // Symfony 5.4+
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $data = $this->requestStack->getMainRequest()->query;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        } else if (method_exists($this->requestStack, 'getMasterRequest')) {
            $data = $this->requestStack->getMasterRequest()->query;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function preparePost()
    {
        $result = [];

        // Symfony 5.4+
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $data = $this->requestStack->getMainRequest()->request;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        } else if (method_exists($this->requestStack, 'getMasterRequest')) {
            $data = $this->requestStack->getMasterRequest()->request;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        }
        return $result;
    }

    /**
     * @return array
     */
    public function prepareCookies()
    {
        $result = [];

        // Symfony 5.4+
        if (method_exists($this->requestStack, 'getMainRequest')) {
            $data = $this->requestStack->getMainRequest()->cookies;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        } else if (method_exists($this->requestStack, 'getMasterRequest')) {
            $data = $this->requestStack->getMasterRequest()->cookies;
            $k = $data->count() . ' ' . self::LABEL_ITEMS;
            $result = [
                $k => $data->all(),
            ];
        }
        return $result;
    }
}