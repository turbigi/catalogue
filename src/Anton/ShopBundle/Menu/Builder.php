<?php
namespace Anton\ShopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $menu->setChildrenAttributes(array('class' => 'nav nav-pills'));
        $name = $options['username'];
        $menu->addChild($name, array('route' => 'AntonShopBundle_homepage', 'attributes' => array('class' => 'dropdown pull-right btnn', 'caret' => 'lol')));
        $menu[$name]->setLinkAttributes(array('class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'));
        $menu[$name]->setChildrenAttributes(array('class' => 'dropdown-menu'));

        $menu[$name]->addChild('Dashboard', array('route' => 'AntonShopBundle_homepage'));
        $menu[$name]->addChild(NULL, array('attributes' => array('class' => 'divider')));
        $menu[$name]->addChild('Exit', array('route' => 'logout'));

        return $menu;
    }
}