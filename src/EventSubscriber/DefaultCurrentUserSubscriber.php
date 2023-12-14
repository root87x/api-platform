<?php

namespace App\EventSubscriber;

use App\Entity\Interfaces\DefaultCurrentUserInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultCurrentUserSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {}

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(PrePersistEventArgs $args)
    {
        if (false === $this->support($args)) {
            return;
        }

        /** @var DefaultCurrentUserInterface $entity */
        $entity = $args->getObject();

        $entity->setUser($this->tokenStorage->getToken()?->getUser());
    }

    private function support(PrePersistEventArgs $args): bool
    {
        /** @var DefaultCurrentUserInterface $entity */
        $entity = $args->getObject();

        return (
            $entity instanceof DefaultCurrentUserInterface
            && null === $entity->getUser()
        );
    }
}
