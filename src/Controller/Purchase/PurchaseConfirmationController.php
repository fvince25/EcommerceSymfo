<?php
namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Event\PurchaseSuccessEvent;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController {


    protected $persister;

    public function __construct(PurchasePersister $persister)
    {
        $this->persister = $persister;
    }


    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request, CartService $cartService, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher) {

        // 1. Nous voulons lire les données du formulaire FormFactoryInterface / Request

        $form = $this->createForm(CartConfirmationType::class);

        // Important !!!! : Ici on n'utilise pas du tout isSubmitted, parce qu'on redirige vers une autre route !
        // C'est le handleRequest qui va s'occuper de récupérer le contenu des données !!

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if(!$form->isSubmitted()) {

            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }

        // 4. Si il n'y a pas de produits dans mon panier : dégager (CartService)
        $cartItems = $cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash("warning",'Vous ne pouvez pas confirmer une commande avec un panier vide');

            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une Purchase . Pour rappel no a pas besoin
        // de copier les champs un à un parce que le form "CartConfirmationType"
        // a une classe "Purchase" dans configureOptions
        //

        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePersister($purchase);

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $em->flush();
        $this->addFlash('success', "La commande a bien été enregistrée");

        $cartService->empty();

        // Lancer un évènement qui permette aux autres devs de réagir à la prise d'une commande.
//        Utilisation de eventdispatcher.

        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $eventDispatcher->dispatch($purchaseEvent, 'purchase.success');

        // Les évènements sont là pour nous faire respecter les principes SOLID.
        // Dans le cas présent, si on a à envoyer un sms au lieu d'un email plus tard,
        // on ne touche pas à la classe du controller.

        // 9. Redirection vers l'index.
        return $this->redirectToRoute("purchase_index");

    }
}