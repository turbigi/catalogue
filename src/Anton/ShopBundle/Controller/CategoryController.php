<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\CategoryType;
use Anton\ShopBundle\Entity\Category;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}/edit", name="categoryEdit")
     */
    public function categoryEditAction(Request $request, $id = null)
    {
        $childs = [];
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository('AntonShopBundle:Category')->findById($id);
        if (!$category) {
            return $this->redirect($this->generateUrl('catalogue'));
        }
        $categories = $entityManager->getRepository('AntonShopBundle:Category')->findAll();
        $this->createArrayOfChilds($category, $childs);

        $arrayWithoutChilds = array_udiff($categories, $childs,
            function ($objA, $objB) {
                return $objA->getId() - $objB->getId();
            }
        );

        $category = $entityManager->getRepository('AntonShopBundle:Category')->findOneById($id);
        $form = $this->createForm(CategoryType::class, $category, ['parents' => $arrayWithoutChilds]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('cache.app')->deleteItem('cache_categories');
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('catalogue');
        }

        return $this->render(
            'AntonShopBundle:Page:categoryEdit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/category/add/{id}", name="categoryAdd")
     */
    public function categoryAddAction(Request $request, $id = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        if ($id != 'root') {
            $category = $entityManager->getRepository('AntonShopBundle:Category')->findOneById($id);
            if (!$category) {
                return $this->redirectToRoute('catalogue');
            }
        }
        $newCategory = new Category();
        $form = $this->createForm(CategoryType::class, $newCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('cache.app')->deleteItem('cache_categories');
            if ($id === 'root') {
                $newCategory->setParent(null);
            } else {
                $newCategory->setParent($category);
            }
            $entityManager->persist($newCategory);
            $entityManager->flush();
            return $this->redirectToRoute('catalogue');
        }
        return $this->render(
            'AntonShopBundle:Page:categoryEdit.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/category/{id}/remove", name="categoryRemove")
     */

    public function categoryRemoveAction(Request $request, $id = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $entityManager->getRepository('AntonShopBundle:Category')->findOneById($id);
        if (!$category) {
            return $this->redirect($this->generateUrl('catalogue'));
        }
        $entityManager->remove($category);
        $entityManager->flush();
        $this->get('cache.app')->deleteItem('cache_categories');
        return $this->redirectToRoute('catalogue');
    }

    public function createArrayOfChilds($categories, &$childs)
    {
        foreach ($categories as $category) {
            if ($category->getChildren()) {
                $this->createArrayOfChilds($category->getChildren()->toArray(), $childs);
                $childs[] = $category;
            }
        }
        return $childs;
    }

}
