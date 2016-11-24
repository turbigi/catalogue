<?php

namespace Anton\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\ProductType;
use Anton\ShopBundle\Entity\Product;


class ProductController extends Controller
{
    /**
     * @Route("/product/page/{sku}", name="pageProduct")
     */
    public function productPageAction($sku = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository('AntonShopBundle:Product')->findOneBySku($sku);
        if (!$product) {
            return $this->redirectToRoute('catalogue');
        }
        return $this->render('product/productPage.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/product/add/{categoryId}", name="newProduct")
     */
    public function productNewAction(Request $request, $categoryId = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        if ($categoryId) {
            $category = $entityManager->getRepository('AntonShopBundle:Category')->findOneById($categoryId);
            $product->setCategory($category);
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $product->getPicture();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter('pictures_directory'),
                $fileName
            );
            $product->setCreatedAt(new \DateTime());
            $product->setUpdatedAt(new \DateTime());

            $product->setSku(uniqid());
            $product->setPicture($fileName);

            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('catalogue');
        }

        return $this->render('product/productNew.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/product/{sku}/edit", name="editProduct")
     */
    public function productEditAction(Request $request, $sku = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository('AntonShopBundle:Product')->findOneBySku($sku);
        if (!$product) {
            return $this->redirectToRoute('catalogue');
        }
        $product->setPicture(
            new File($this->getParameter('pictures_directory') . '/' . $product->getPicture())
        );
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $product->getPicture();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('pictures_directory'),
                $fileName
            );

            $product->setPicture($fileName);
            $product->setUpdatedAt(new \DateTime());

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('catalogue');
        }

        return $this->render('product/productNew.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/product/{sku}/remove", name="removeProduct")
     */
    public function productRemoveAction(Request $request, $sku = null)
    {
        $entityManager  = $this->getDoctrine()->getManager();
        $product = $entityManager ->getRepository('AntonShopBundle:Product')->findOneBySku($sku);
        if (!$product) {
            return $this->redirectToRoute('catalogue');
        }
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('catalogue');
    }
}
