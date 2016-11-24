<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\User;

class AdminPageController extends Controller
{
    /**
     * @Route("/admin/user/add", name="adminUserAdd")
     */

    public function addNewUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['role' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setCreatedAt(new \DateTime());
            $user->setPassword($password);
            $username = $user->getUsername();
            $user->setAccessToken(base64_encode(md5($password . $username)));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('homepage');
        }
        return $this->render(
            'AntonShopBundle:Page:signup.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/admin", name="adminPage")
     */
    public function adminAction(Request $request)
    {
        return $this->render('admin.html.twig');
    }

    /**
     * @Route("/ajax/users", name="adminAjaxUsers")
     */
    public function ajaxUsersAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        if ($request->isMethod('GET')) {
            if (!($sortByField = $request->query->get('sortbyfield'))) {
                $sortByField = "id_user";
            }
            if (!($order = $request->query->get('order'))) {
                $order = "";
            }
            if (!($rows = $request->query->get('rows'))) {
                $rows = 0;
            }
            if (!$page = $request->query->get('page')) {
                $page = 0;
            }
            if (!($filterByField = $request->query->get('filterbyfield'))) {
                $filterByField = "id_user";
            }
            if (!($pattern = $request->query->get('pattern'))) {
                $pattern = "%%";
            }

            $begin = $rows * $page;

            if ($rows === 0) {
                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:User u');
            } else {

                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:User u WHERE u.'
                    . $filterByField . ' like ' . "'" . $pattern . "'"
                    . ' order by u.' . $sortByField . ' ' . $order)
                    ->setMaxResults($rows)
                    ->setFirstResult($begin);
            }

            $users = $query->getResult();
            $serializer = $this->container->get('jms_serializer');
            $jsonContent = $serializer
                ->serialize(
                    $users,
                    'json'
                );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($jsonContent);
            return $response;

        } else {
            return $this->redirect($this->generateUrl('catalogue'));
        }
    }

    /**
     * @Route("/ajax/products", name="adminAjaxProducts")
     */
    public function ajaxProductsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getEntityManager();
        if ($request->isMethod('GET')) {
            if (!($sortByField = $request->query->get('sortbyfield'))) {
                $sortByField = "id";
            }
            if (!($order = $request->query->get('order'))) {
                $order = "";
            }
            if (!($rows = $request->query->get('rows'))) {
                $rows = 0;
            }
            if (!$page = $request->query->get('page')) {
                $page = 0;
            }
            if (!($filterByField = $request->query->get('filterbyfield'))) {
                $filterByField = "id";
            }
            if (!($pattern = $request->query->get('pattern'))) {
                $pattern = "%%";
            }

            $begin = $rows * $page;
            if ($rows === 0) {
                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:Product u');
            } else {

                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:Product u WHERE u.'
                    . $filterByField . ' like ' . "'" . $pattern . "'"
                    . ' order by u.' . $sortByField . ' ' . $order)
                    ->setMaxResults($rows)
                    ->setFirstResult($begin);
            }

            $users = $query->getResult();
            $serializer = $this->container->get('jms_serializer');
            $jsonContent = $serializer
                ->serialize(
                    $users,
                    'json'
                );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($jsonContent);
            return $response;

        } else {
            return $this->redirect($this->generateUrl('catalogue'));
        }
    }
}
