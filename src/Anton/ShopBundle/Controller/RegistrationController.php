<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\User;
use Anton\ShopBundle\Entity\Role;

class RegistrationController extends Controller
{
    /**
     * @Route("/signup", name="AntonShopBundle_signup")
     */

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $role = new Role();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setSalt(md5(time()));

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $role->setName('ROLE_USER');

            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $user->getUserRoles()->add($role);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('AntonShopBundle_homepage');
        }

        return $this->render(
            'AntonShopBundle:Page:signup.html.twig',
            array('form' => $form->createView())
        );
    }
}
