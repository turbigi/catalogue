<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CatalogueController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */

    public function homepageAction(Request $request)
    {

        return $this->render('AntonShopBundle:Page:index.html.twig');
    }

    /**
     * @Route("/catalogue/category/{id}", name="category")
     */
    public function categoriesAction(Request $request, $id = null)
    {
        $cacheCategories = $this->get('cache.app')->getItem('cache_categories');
        $entityManager = $this->getDoctrine()->getManager();

        $treeOfCategories = $this->checkCache($cacheCategories, $entityManager);

        if (!$id) {
            return $this->redirectToRoute('catalogue');
        } else {
            $category = $entityManager->getRepository('AntonShopBundle:Category')->findOneById($id);
            if (!$category) {
                dump('');
                return $this->redirectToRoute('catalogue');
            }
            $products = $category->getProducts();
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            1
        );
        return $this->render('AntonShopBundle:Page:categories.html.twig', ['pagination' => $pagination, 'categories' => $treeOfCategories, 'products' => $products]);
    }

    /**
     * @Route("/catalogue", name="catalogue")
     */
    public function catalogueAction(Request $request, $categoryId = null)
    {
        $cacheCategories = $this->get('cache.app')->getItem('cache_categories');
        $entityManager = $this->getDoctrine()->getManager();
        $treeOfCategories = $this->checkCache($cacheCategories, $entityManager);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            [],
            $request->query->getInt('page', 1),
            1
        );
        return $this->render('AntonShopBundle:Page:categories.html.twig', ['pagination' => $pagination, 'categories' => $treeOfCategories]);
    }

    public function checkCache($cacheCategories, $entityManager)
    {
        if (!$cacheCategories->isHit()) {
            $categories = $entityManager->getRepository('AntonShopBundle:Category')->findByParent(NULL);
            $treeOfCategories = $this->buildTree($categories);
            $this->get('cache.app')->save($cacheCategories->set($treeOfCategories));
        } else {
            $treeOfCategories = $cacheCategories->get();
        }
        return $treeOfCategories;
    }

    public function buildTree($categories)
    {
        $branch = [];
        foreach ($categories as $category) {
            if ($category->getChildren()) {
                $this->buildTree($category->getChildren()->toArray());
                $branch[] = $category;
            }
        }
        return $branch;
    }
}
