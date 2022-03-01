<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {

        dump($utils);

        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);

        dump($utils->getLastAuthenticationError(), $utils->getLastUsername());
        // Si on ne fait rien dans le LoginFormAuthenticator (onAuthenticationFailure), cette fonction ne marchera pas.

        $formview = $form->createView();

        return $this->render('security/login.html.twig', [
            'formView' => $formview,
            'error' => $utils->getLastAuthenticationError()
        ]);
    }
}
