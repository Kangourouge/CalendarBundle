<?php

namespace KRG\CalendarBundle\Listener;

use KRG\CalendarBundle\Model\CalendarModelInterface;
use KRG\CalendarBundle\Model\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Twig\Template;

class CalendarListener
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        /* @var Template $template */
        $request = $event->getRequest();

        $result = $event->getControllerResult();
        if ($result instanceof CalendarModelInterface) {
            if ($request->isXmlHttpRequest()) {
                $events = array_map(function (Event $event) {
                    return $event->toArray();
                }, $result->getEvents());
                $event->setResponse(new JsonResponse($events));
            } else {
                $event->setControllerResult(['calendar' => $result]);
            }
        }
    }
}
