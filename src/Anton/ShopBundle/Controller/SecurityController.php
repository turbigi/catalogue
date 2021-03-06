<?php

namespace Anton\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anton\ShopBundle\Form\RecoveryType;
use Anton\ShopBundle\Entity\User;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="securityLogin")
     */
    public function loginAction()
    {
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('homepage');
        }
        $helper = $this->get('security.authentication_utils');
        return $this->render('security/login.html.twig', [
            'error' => $helper->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login/check", name="securityLoginCheck")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/recovery/confirm", name="recoveryConfirm")
     */
    public function passwordRecoveryConfirmAction(Request $request)
    {
        $apiKey = $request->query->get('apikey');
        if ($apiKey) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository('AntonShopBundle:User')->findOneByApiKey($apiKey);
            if (!$user) {
                return $this->redirectToRoute('homepage');
            }
            $form = $this->createForm(RecoveryType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $username = $user->getUsername();
                $password = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $user->setApiKey(base64_encode(md5($user->getPlainPassword() . $username)));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Your password has been changed successfully');
                return $this->redirectToRoute('securityLogin');
            }
            return $this->render(
                'security/recovery_pass_confirm.html.twig', [
                'form' => $form->createView(),
                'apiKey' => $apiKey,
            ]);
        }

    }

    /**
     * @Route("/recovery", name="recoveryPassword")
     */
    public function passwordRecoveryAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('_email');
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository('AntonShopBundle:User')->findOneByEmail($email);
            if (!$user) {
                $this->addFlash('error', 'Email not found.');
                return $this->redirectToRoute('recoveryPassword');
            }
            $message = \Swift_Message::newInstance()
                ->setSubject('Recovery password')
                ->setFrom('zerg7007@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('mail/mailer.html.twig', ['enquiry' => $user]), 'text/html');
            $this->get('mailer')->send($message);
            $this->addFlash('success', 'Message has been sent successfully.');
            return $this->render('security/recovery_password.html.twig');
        }
        return $this->render('security/recovery_password.html.twig');
    }
}
