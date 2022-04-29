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

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Output
 * 
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class Output
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Generator
     */
    private $generator;

    /**
     * Output constructor.
     * @param RequestStack $requestStack
     * @param Generator $generator
     */
    public function __construct(RequestStack $requestStack, Generator $generator)
    {
        $this->requestStack = $requestStack;
        $this->generator = $generator;
    }

    /**
     * @param ResponseEvent $event
     * @throws \ReflectionException
     */
    public function handle(ResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->isXmlHttpRequest()
            || $request->attributes->get('_route') == '_wdt'
            || $request->attributes->get('_route') == '_profiler'
            || $this->requestStack->getParentRequest() !== null) {
            return;
        }

        $response = $event->getResponse();
        if ($response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse
            || $response instanceof \Symfony\Component\HttpFoundation\StreamedResponse
            || $response instanceof \Symfony\Component\HttpFoundation\JsonResponse
            || $response instanceof \Symfony\Component\HttpFoundation\RedirectResponse
            || $response->headers->has('Content-Disposition')) {
            return;
        }

        $data = $this->generator->generate($event);
        if ($data === false) {
            return;
        }
        
        $content = $response->getContent();
        if (strpos($content, '</body>') !== false) {
            $content = str_replace('</body>', $data . '</body>', $content);
        }

        $response->setContent($content);
    }
}