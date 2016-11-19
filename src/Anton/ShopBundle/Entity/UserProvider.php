<?php
namespace Anton\ShopBundle\Entity;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManager;

class UserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('AntonShopBundle:User')->findOneBy(['username' => $username]);
        if (!$user) {
            throw new UsernameNotFoundException();
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return 'Anton\ShopBundle\Entity\User' === $class;
    }
}