<?php
namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController {

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request, CartService $cartService, EntityManagerInterface $em) {

        // 1. Nous voulons lire les données du formulaire FormFactoryInterface / Request

        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager

        if(!$form->isSubmitted()) {

            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }

        // 3. Si je ne suis pas connecté : dégager (Sécurity)

        $user = $this->getUser();
        $cartItems = $cartService->getDetailedCartItems();

        // 4. Si il n'y a pas de produits dans mon panier : dégager (CartService)

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


        // 6. Nous allons lier avec l'utilsiateur actuellement connecté (Security) + le datetime

        $purchase->setUser($user)
        ->setPurchasedAt(new \DateTime())
            ->setTotal($cartService->getTotal());

        $em->persist($purchase);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)

        foreach ($cartService->getDetailedCartItems() as $cartItem) {

            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $em->persist($purchaseItem);

        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)

        $em->flush();
        $this->addFlash('success', "La commande a bien été enregistrée");

        $cartService->empty();

        // 9. Redirection vers l'index.

        return $this->redirectToRoute("purchase_index");










    }
}