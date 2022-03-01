<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $utils, FormFactoryInterface $factory): Response
    {

        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);

        $formview = $form->createView();

        // Si on ne fait rien dans le LoginFormAuthenticator (onAuthenticationFailure), cette fonction ne marchera pas.
        // Attention la méthode getLastAuthenticationError "consomme" son contenu, il est vide après exécution

        return $this->render('security/login.html.twig', [
            'formView' => $formview,
            'error' => $utils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout",name="security_logout")
     */
    public function logout() {

    }
}
