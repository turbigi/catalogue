<?php

namespace Anton\ShopBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\SecurityEvents;
use Doctrine\ORM\EntityManager;

class LastLoginTime implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLoginTime(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);
    }

    public static function getSubscribedEvents()
    {
        return array(SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin');
    }
}
