<?php
namespace Anton\ShopBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Knp\Menu\FactoryInterface;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttributes(['class' => 'nav nav-pills']);
        $name = $options['username'];
        $menu->addChild($name, ['route' => 'homepage', 'attributes' => ['class' =>
            'dropdown pull-right', 'caret' => 'userCaret']]);
        $menu[$name]->setLinkAttributes(['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']);
        $menu[$name]->setChildrenAttributes(['class' => 'dropdown-menu']);
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $menu[$name]->addChild('Users', ['route' => 'usersManagement']);
            $menu[$name]->addChild('Products', ['route' => 'productsManagement']);
            $menu[$name]->addChild(NULL, ['attributes' => ['class' => 'divider']]);
        } elseif ($this->container->get('security.authorization_checker')->isGranted('ROLE_MODERATOR')) {
            $menu[$name]->addChild('Products', ['route' => 'productsManagement']);
            $menu[$name]->addChild(NULL, ['attributes' => ['class' => 'divider']]);
        }

        $menu[$name]->addChild('Logout', ['route' => 'logout']);

        $menu->addChild('Catalogue', ['route' => 'catalogue', 'attributes' => ['class' =>
            'pull-left']]);
        return $menu;
    }
}