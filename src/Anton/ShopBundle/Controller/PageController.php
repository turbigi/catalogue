<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anton\ShopBundle\Form\EnquiryType;
use Anton\ShopBundle\Entity\Enquiry;

class PageController extends Controller
{
    /**
     * @Route("/", name="AntonShopBundle_homepage")
     */

    public function indexAction()
    {
        return $this->render('AntonShopBundle:Page:index.html.twig');
    }
}