<?php

namespace Szczyglis\ExtendedDumpBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Szczyglis\ExtendedDumpBundle\Core\Output;

/**
 * ResponseSubscriber
 * 
 * @package szczyglis/extended-dump-bundle
 * @author Marcin Szczyglinski <szczyglis@protonmail.com>
 * @copyright 2022 Marcin Szczyglinski
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 * @link https://github.com/szczyglis-dev/extended-dump-bundle
 */
class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var Output
     */
    private $output;

    /**
     * ResponseSubscriber constructor.
     * @param Output $output
     */
    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    /**
     * @param ResponseEvent $event
     * @throws \ReflectionException
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $this->output->handle($event);
    }
}

require_once(__DIR__ . '/../Functions/xdump.php');