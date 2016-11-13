<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomePageController extends Controller
{
    /**
     * @Route("/", name="AntonShopBundle_homepage")
     */

    public function indexAction(Request $request)
    {
        return $this->render('AntonShopBundle:Page:index.html.twig');
    }

}