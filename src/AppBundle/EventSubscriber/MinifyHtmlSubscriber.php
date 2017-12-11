<?php
namespace AppBundle\EventSubscriber;

use IvoPetkov\HTML5DOMDocument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Wa72\HtmlPrettymin\PrettyMin;

class MinifyHtmlSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => ['onKernelResponse', -50],
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');


        if ($route !== 'app_default_update') {
            $response = $event->getResponse();
            $contents = $response->getContent();

            $min = new PrettyMin();
            $doc = new HTML5DOMDocument();

            $doc->loadHTML($contents);
            $min->load($doc)->minify();

            $response->setContent($doc->saveHTML());
        }
    }
}