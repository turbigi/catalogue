<?php

namespace Anton\ShopBundle\Controller;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\User;

class RegistrationController extends Controller
{
    /**
     * @Route("/signup", name="AntonShopBundle_signup")
     */

    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setCreatedAt(new \DateTime());
            $user->setPassword($password);
            $user->setRole('ROLE_USER');
            $username = $user->getUsername();
            $user->setAccessToken(base64_encode(md5($password.$username)));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $guardHandler = $this->container->get('security.authentication.guard_handler');
            $guardHandler->authenticateUserAndHandleSuccess($user,$request,$this->get('app.form_login_authenticator'),'main');

            return $this->redirectToRoute('AntonShopBundle_homepage');
        }
        return $this->render(
            'AntonShopBundle:Page:signup.html.twig',
            array('form' => $form->createView())
        );
    }
}
