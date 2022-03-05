<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{

    protected $security;
    protected $entityManager;
    protected $cartService;

    public function __construct(Security $security, EntityManagerInterface $entityManager, CartService $cartService)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;

    }

    public function storePersister(Purchase $purchase)
    {

        // 6. Nous allons lier avec l'utilsiateur actuellement connecté (Security) + le datetime
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new \DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->entityManager->persist($purchase);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {

            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $this->entityManager->persist($purchaseItem);

        }


    }

}