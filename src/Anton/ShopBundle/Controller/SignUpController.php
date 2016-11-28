<?php

namespace Anton\ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Anton\ShopBundle\Form\UserType;
use Anton\ShopBundle\Entity\User;

class SignUpController extends Controller
{
    /**
     * @Route("/signup", name="signUp")
     */

    public function signUpAction(Request $request)
    {
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setLastLoginTime(new \DateTime());
            $user->setPassword($password);
            $user->setRole('ROLE_USER');
            $username = $user->getUsername();
            $user->setApiKey(base64_encode(md5($password . $username)));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'You have successfully registered!');
            return $this->redirectToRoute('securityLogin');
        }
        return $this->render(
            'signup/signup.html.twig', [
                'form' => $form->createView(),
                'title_text' => 'Registration',
                'header_text' => 'Please Sign Up',
            ]
        );
    }
}
