<?php

namespace Anton\ShopBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

class AdminPageController extends Controller
{
    /**
     * @Route("/admin", name="admin_page")
     */
    public function adminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AntonShopBundle:Category');

        /**
         * @var categoryOne Category
         */

        $em = $this->getDoctrine()->getManager();
        $data = array();
        $categories = $em
            ->createQueryBuilder('c')
            ->select('c')->from('Anton\ShopBundle\Entity\Category', 'c')
            ->getQuery()
            ->getResult();
        $cat = $em->getRepository('Anton\ShopBundle\Entity\Category')->find(1)->getChildren();
        $lul = $this->lol($cat, 1);
        $categoryOne = $em->createQueryBuilder()->select('p')->from('Anton\ShopBundle\Entity\Category', 'p')->andWhere('p.parent IS NULL')->getQuery();
        dump($lul);
    }

    public function lol(PersistentCollection $categories, $parentId = 1)
    {
        $branch = array();
        foreach ($categories as $element) {
            if ($element->getParent()->getId() == $parentId) {
                $children = $this->lol($element->getChildren(), $element->getId());
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
