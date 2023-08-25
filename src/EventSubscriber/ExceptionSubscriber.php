<?php
// src/EventSubscriber/ExceptionSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10]
            ]
        ];
    }

    public function processException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof \RuntimeException) {
            if ($exception->getMessage() === "User not logged in.") {
                $response = new RedirectResponse($this->router->generate('app_login'));
                $event->setResponse($response);
            } elseif ($exception->getMessage() === "User not authorized for this RDV.") {
                $response = new RedirectResponse($this->router->generate('admin'));
                $event->setResponse($response);
            }
        }
    }
}
