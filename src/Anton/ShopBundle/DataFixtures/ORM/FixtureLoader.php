<?php
namespace Anton\ShopBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Anton\ShopBundle\Entity\User;
use Anton\ShopBundle\Entity\Role;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\Common\Persistence\ObjectManager;
class FixtureLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // создание роли ROLE_ADMIN
        $role = new Role();
        $role->setName('ROLE_MODERATOR');

        $manager->persist($role);

        // создание пользователя
        $user = new User();
        $user->setUsername('turbigi5');
        $user->setEmail('john2@exa5mple.com');
        $user->setSalt(md5(time()));

        // шифрует и устанавливает пароль для пользователя,
        // эти настройки совпадают с конфигурационными файлами
        $encoder = new MessageDigestPasswordEncoder('md5', true, 10);
        $password = $encoder->encodePassword('admin', $user->getSalt());
        $user->setPassword($password);

        $user->getUserRoles()->add($role);

        $manager->persist($user);
        $manager->flush();

        // ...

    }
}