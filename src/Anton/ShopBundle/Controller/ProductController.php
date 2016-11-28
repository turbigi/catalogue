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
        return $this->render('product/page_product.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/add/{categoryId}", name="newProduct")
     */
    public function productNewAction(Request $request, $categoryId = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        if ($categoryId) {
            $product->setCategory($entityManager->getReference('Anton\ShopBundle\Entity\Category', $categoryId));
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

        return $this->render('product/new_product.html.twig', [
            'form' => $form->createView(),
            'title_text' => 'New product',
        ]);
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
        $oldImagePath = $product->getPicture();
        $product->setPicture(
            new File($this->getParameter('pictures_directory') . '/' . $product->getPicture())
        );
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filePath = $this->getParameter('pictures_directory').'/'.$oldImagePath;
            if(file_exists($filePath)) unlink($filePath);
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

        return $this->render('product/new_product.html.twig', [
            'form' => $form->createView(),
            'title_text' => 'Edit product',
        ]);
    }

    /**
     * @Route("/product/{sku}/remove", name="removeProduct")
     */
    public function productRemoveAction(Request $request, $sku = null)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = $entityManager->getRepository('AntonShopBundle:Product')->findOneBySku($sku);
        if (!$product) {
            return $this->redirectToRoute('catalogue');
        }
        $entityManager->remove($product);
        $entityManager->flush();
        return $this->redirectToRoute('catalogue');
    }
}
