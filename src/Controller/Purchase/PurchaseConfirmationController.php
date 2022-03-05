<?php
namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController {

protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;

    }


    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag, CartService $cartService, EntityManagerInterface $em) {

        // 1. Nous voulons lire les données du formulaire FormFactoryInterface / Request

        $form = $this->formFactory->create(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager

        if(!$form->isSubmitted()) {
            // Message Flash puis redirection (FlashBagInterface
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }



        // 3. Si je ne suis pas connecté : dégager (Sécurity)

        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour pouvoir passer une commande");
        }

        $cartItems = $cartService->getDetailedCartItems();



        // 4. Si il n'y a pas de produits dans mon panier : dégager (CartService)

        if (count($cartItems) === 0) {
            $flashBag->add("warning",'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une Purchase . Pour rappel no a pas besoin
        // de copier les champs un à un parce que le form "CartConfirmationType"
        // a une classe "Purchase" dans configureOptions
        //
        /** @var Purchase */
        $purchase = $form->getData();


        // 6. Nous allons lier avec l'utilsiateur actuellement connecté (Security) + le datetime

        $purchase->setUser($user)
        ->setPurchasedAt(new \DateTime());

        $em->persist($purchase);

        $total = 0;


        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)

        foreach ($cartService->getDetailedCartItems() as $cartItem) {

            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $total += $cartItem->getTotal();
            $em->persist($purchaseItem);

        }

        $purchase->setTotal($total);

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)

        $em->flush();

        $flashBag->add('success', "La commande a bien été enregistrée");

        // 9. Redirection vers l'index.

        return $this->redirectToRoute("purchase_index");










    }
}