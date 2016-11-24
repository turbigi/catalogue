<?php

namespace Anton\ShopBundle\EventListener;

use Anton\ShopBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LastLoginTime implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $user->setCreatedAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush($user);
    }
    public static function getSubscribedEvents()
    {
        return array(SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin');
    }
}