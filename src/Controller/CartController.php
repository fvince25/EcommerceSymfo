<?php

namespace App\Controller;


use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService, Request $request): Response
    {

        $product = $productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $cartService->add($id);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");

        if($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);

    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {

        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);

        // $form->createView() beaucoup plus spécialisé dans l'affichage que formview.
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     */
    public function delete($id, ProductRepository $productRepository, CartService $cartService) {

        $product = $productRepository->find($id);
        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }
        $cartService->remove($id);
        $this->addFlash('warning', "Le produit a bien été retiré au panier");
        return $this->redirectToRoute('cart_show');

    }
    /**
     * @Route("/cart/decremente/{id}", name="cart_decremente", requirements={"id":"\d+"})
     */
    public function decremente($id, ProductRepository $productRepository, CartService $cartService) {

        $product = $productRepository->find($id);

        if(!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté !");
        }
        $cartService->decremente($id);

        $this->addFlash('warning', "Le produit a bien été décrémenté au panier");

        return $this->redirectToRoute('cart_show');

    }

}
