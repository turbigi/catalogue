<?php
namespace Anton\ShopBundle\Security;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
class ApiKeyUserProvider implements UserProviderInterface
{
    private $em;
    public function __constructor(EntityManager $em)
    {
        $this->em = $em;
    }
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('AntonShopBundle:User')->findOneByUsername($username);
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