<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\LogoutEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CustomLogoutSubscriber implements EventSubscriberInterface
{
    public function processLogout (LogoutEvent $event) 
    {
      //  $event::getSubscribedEvents; //??

        $request = $event->getRequest();

        $referer = $request->headers->get('referer');
        
        return new RedirectResponse($referer);  //??? from old code for success_handler for logout - now  deprecated

        return $this->redirectToRoute($referer);

    }
}