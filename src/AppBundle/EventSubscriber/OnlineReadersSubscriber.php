<?php
/**
 * @package platform
 * @author Petru Szemereczki <petru.office92@gmail.com>
 * @since 1.0
 */

namespace AppBundle\EventSubscriber;


use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class OnlineReadersSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        // setting the shit for "online users"
        if (!$request->cookies->has('__visitor_id')) {
            $visitor = new Cookie('__visitor_id', Uuid::uuid4()->toString(), new \DateTime('+5 minutes'));
            $response->headers->setCookie($visitor);
        }
    }
}