<?php
namespace Anton\ShopBundle\DataFixtures\ORM;

use Anton\ShopBundle\Entity\Category;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Anton\ShopBundle\Entity\User;
use Anton\ShopBundle\Entity\Role;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\Common\Persistence\ObjectManager;
class FixtureLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categoryr = new Category();
        $categoryr->setIsActive(1);
        $categoryr->setName('root');

        $category = new Category();
        $category->setIsActive(1);
        $category->setName('Овощи и фрукты');

        $category2 = new Category();
        $category2->setIsActive(1);
        $category2->setName('Молочные продукты,яйца');

        $category3 = new Category();
        $category3->setIsActive(1);
        $category3->setName('Овощи');

        $category4 = new Category();
        $category4->setIsActive(1);
        $category4->setName('Баклажан');

        $category5 = new Category();
        $category5->setIsActive(1);
        $category5->setName('Зелень');

        $category6 = new Category();
        $category6->setIsActive(1);
        $category6->setName('Яйцо');

        $category7 = new Category();
        $category7->setIsActive(1);
        $category7->setName('Яйцо куриное');

        $category8 = new Category();
        $category8->setIsActive(1);
        $category8->setName('Яйцо перепелиное');
        $category9 = new Category();
        $category9->setIsActive(1);
        $category9->setName('fffff');

        $category->setParent($categoryr);
        $category2->setParent($categoryr);
        $category3->setParent($category);
        $category4->setParent($category3);
        $category5->setParent($category3);

        $category6->setParent($category2);
        $category7->setParent($category6);

        $category8->setParent($category6);
        $category9->setParent($category4);


        $manager->persist($categoryr);
        $manager->persist($category);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->persist($category5);
        $manager->persist($category6);
        $manager->persist($category7);
        $manager->persist($category8);

        $manager->flush();
        // создание роли ROLE_ADMIN
        /**$role = new Role();
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
        $manager->flush();**/

        // ...

    }
}