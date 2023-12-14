<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();

        if ($event->getUser() instanceof UserInterface) {
            $payload['uuid'] = $event->getUser()->getId();
        }

        $event->setData($payload);
    }
}