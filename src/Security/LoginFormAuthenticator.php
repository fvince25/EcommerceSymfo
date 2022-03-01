<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{

    protected $encoder;

    // Comme d'hab pour se faire livrer un service, il faut passer par un constructeur.
    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }


    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $request->request->get('login');
        // dd($request);
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        // Il va voir en base de données si l'email existe bien ...

        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            // On court circuite l'exception normale
            throw new AuthenticationException("Cette adresse email n'est pas connue");
        }
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Vérifier que le mot de passe fourni, correspond bien au mot de passe de la base de données.
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);

        if (!$isValid) {
            // On court circuite l'exception normale

            throw new AuthenticationException("Les informations de connexion ne correspondent pas");
        }

        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        //A priori, Il n'y a pas besoin de rediriger : Comme la requête en POST continue, on revient vers le formulaire.

        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
        // -> permet de faire marcher $utils->getLastAuthenticationError() dans SecurityController. (très important)

        $request->attributes->set(Security::LAST_USERNAME, $request->request->get("login")['email']);
        // De même pour $utils->getLastUsername() !!!!!
        // ATTENTION !!! Contrairement au cours de Lior, j'ai dû faire une surcharge au niveau de Security::LAST_USERNAME

    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // On ne peut pas utiliser redirectToRoute, car on extend pas AbstractConroller comme dans productController
        return new RedirectResponse('/');

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
