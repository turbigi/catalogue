<?php
namespace Anton\ShopBundle\Entity;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

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
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless

    }

    public function supportsClass($class)
    {
        return 'Anton\ShopBundle\Entity\User' === $class;
    }
}