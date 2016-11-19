<?php

namespace Anton\ShopBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\EditCategory;
use Doctrine\ORM\PersistentCollection;
use Anton\ShopBundle\Entity\Category;

class HomePageController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */

    public function homepageAction(Request $request)
    {
        return $this->render('AntonShopBundle:Page:index.html.twig');
    }

    /**
     * @Route("/categories/edit/{name}", name="editCategories")
     */

    public function categoriesEditAction(Request $request, $name)
    {
        $alll = [];
        $this->get('cache.app')->deleteItem('cache_categories');
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AntonShopBundle:Category')->findOneByName($name);
        $allcategory = $em->getRepository('AntonShopBundle:Category')->findAll();

        foreach ($allcategory as $all) {
            $alll[] = $all->getName();
        }
        dump($category->getName());
        $form = $this->createForm(EditCategory::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$password = $request->request->get('_passsword');
            $categoryName = $category->getName();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('categories');
        }
        return $this->render(
            'AntonShopBundle:Page:categoryEdit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/categories/add/{name}", name="addCategories")
     */

    public function categoriesAddAction(Request $request, $name)
    {
        $this->get('cache.app')->deleteItem('cache_categories');
        $em = $this->getDoctrine()->getManager();
        $newCategory = new Category();
        $category = $em->getRepository('AntonShopBundle:Category')->findOneByName($name);
        $form = $this->createForm(EditCategory::class, $newCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newCategory->setParent($category);
            $em->persist($category);
            $em->persist($newCategory);
            $em->flush();
            return $this->redirectToRoute('categories');
        }
        return $this->render(
            'AntonShopBundle:Page:categoryEdit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/categories/remove/{name}", name="removeCategories")
     */

    public function categoriesRemoveAction(Request $request, $name)
    {
        $this->get('cache.app')->deleteItem('cache_categories');
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AntonShopBundle:Category')->findOneByName($name);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('categories');
    }


    /**
     * @Route("/categories", name="categories")
     */

    public function categoriesAction(Request $request)
    {
        $numProducts = $this->get('cache.app')->getItem('cache_categories');
        if (!$numProducts->isHit()) {
            $em = $this->getDoctrine()->getManager();
            $cat = $em->getRepository('Anton\ShopBundle\Entity\Category')->findByParent(NULL);
            $lul = $this->lol($cat, 1);
            $this->get('cache.app')->save($numProducts->set($lul));
        } else {
            $lul = $numProducts->get();
        }
        return $this->render('AntonShopBundle:Page:categories.html.twig', ['categories' => $lul]);
    }

    public function lol(Array $categories, $parentId = 1)
    {
        $branch = [];
        foreach ($categories as $element) {
            if ($element->getChildren()) {
                $children = $this->lol($element->getChildren()->toArray(), $element->getId());
                $branch[] = $element;
            }
        }
        return $branch;
    }
}