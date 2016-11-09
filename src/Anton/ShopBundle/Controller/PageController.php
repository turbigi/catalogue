<?php
// src/Blogger/BlogBundle/Controller/PageController.php

namespace Anton\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Entity\Enquiry;
use Anton\ShopBundle\Form\EnquiryType;

class PageController extends Controller
{
    public function indexAction()
    {
        return $this->render('AntonShopBundle:Page:index.html.twig');
    }

    public function loginAction()
    {
        return $this->render('AntonShopBundle:Page:login.html.twig');
    }
}