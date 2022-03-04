<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;


class PurchasesListController extends AbstractController {

    protected $security;
    protected $router;
    protected $twig;

    public function __construct(Security $security, RouterInterface $router, Environment $twig) {
        $this->security = $security;
        $this->router = $router;
        $this->twig = $twig;

    }


    /**
     * @Route("/purchases", name="purchase_index")
     */
    public function index() {

        // Surcharge classe User
        /** @var User */
        $user = $this->security->getUser();

        // Par défaut c'est un userinterface et pas un user !!!!

        if(!$user) {
            // On aurait pu utiliser UrlGeneratorInterface
//            $url = $this->router->generate('homepage');
//            return new RedirectResponse($url);
            // Exception super pratique qui renvoie autmatiquement à la page login, et qui remet sur la page en cours une fois connecté !!!

            throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes");
        }

        $html = $this->twig->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);

        return new Response($html);

    }


}