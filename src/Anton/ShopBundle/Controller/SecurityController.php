<?php

namespace Anton\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Anton\ShopBundle\Entity\UserProvider;
use Anton\ShopBundle\Form\Recovery;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Anton\ShopBundle\Entity\User;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        return $this->render('AntonShopBundle:Security:login.html.twig');
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/recovery_confirm", name="recovery_confirm")
     */
    public function passwordRecoveryConfirmAction(Request $request)
    {


        $apiKey = $request->query->get('apikey');
        if ($apiKey) {
            $em = $this->getDoctrine()->getManager();
            $userr = $em->getRepository('AntonShopBundle:User')->findOneByAccessToken($apiKey);
            $form = $this->createForm(Recovery::class, $userr);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $username = $userr->getUsername();
                $passwordd = $this->get('security.password_encoder')
                    ->encodePassword($userr, $userr->getPlainPassword());
                $userr->setPassword($passwordd);
                $userr->setAccessToken(base64_encode(md5($userr->getPlainPassword().$username)));
                $em->persist($userr);
                $em->flush();
                return $this->redirectToRoute('homepage');
            }
            return $this->render(
                'AntonShopBundle:Page:signup.html.twig',
                array('form' => $form->createView(),'apiKey'=>$apiKey)
            );
        }

    }

    /**
     * @Route("/recovery", name="recovery_password")
     */
    public function passwordRecoveryAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $email = $request->request->get('_email');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AntonShopBundle:User')->findOneByEmail($email);
            $message = \Swift_Message::newInstance()
                ->setSubject('Recovery password')
                ->setFrom('zerg7007@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('AntonShopBundle:Page:contactEmail.html.twig', array('enquiry' => $user)), 'text/html');
            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add('blogger-notice', 'Your contact enquiry was successfully sent. Thank you!');
            return $this->render('AntonShopBundle:Security:recovery.html.twig');
        }
        return $this->render('AntonShopBundle:Security:recovery.html.twig');
    }
}