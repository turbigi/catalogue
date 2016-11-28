<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\ProductType;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\User;
use Anton\ShopBundle\Entity\Product;

class ManagementController extends Controller
{
    /**
     * @Route("/management/users/add", name="managementNewUser")
     */
    public function newUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['role' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setLastLoginTime(new \DateTime());
            $user->setPassword($password);
            $username = $user->getUsername();
            $user->setApiKey(base64_encode(md5($password . $username)));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('usersManagement');
        }
        return $this->render(
            'signup/signup.html.twig', [
                'form' => $form->createView(),
                'title_text' => 'Add new user',
                'header_text' => '',
            ]
        );
    }

    /**
     * @Route("/management/users", name="usersManagement")
     */
    public function managementUsersAction(Request $request)
    {
        return $this->render('management/management.html.twig', ['table' => 'User']);
    }

    /**
     * @Route("/management/products", name="productsManagement")
     */
    public function managementProductsAction(Request $request)
    {
        return $this->render('management/management.html.twig', ['table' => 'Product']);
    }

    /**
     * @Route("/ajax/{table}", name="ajaxManagement")
     */
    public function ajaxUsersAction(Request $request, $table = null)
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

            $offset = $rows * $page;

            if ($rows === 0) {
                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:' . $table . ' u');
            } else {
                $query = $entityManager->createQuery('SELECT u FROM AntonShopBundle:' . $table . ' u WHERE u.'
                    . $filterByField . ' like ' . "'" . $pattern . "'"
                    . ' order by u.' . $sortByField . ' ' . $order)
                    ->setMaxResults($rows)
                    ->setFirstResult($offset);
            }

            $entity = $query->getResult();
            $serializer = $this->container->get('jms_serializer');
            $jsonContent = $serializer
                ->serialize(
                    $entity,
                    'json'
                );
            $response = new Response();
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent($jsonContent);
            return $response;
        } else {
            return $this->redirect($this->generateUrl('homepage'));
        }
    }
}
