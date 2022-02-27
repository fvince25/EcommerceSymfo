<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
//            throw new NotFoundHttpException("La catégorie demandée n'existe pas");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, $category_slug,
                         ProductRepository $productRepository,
                         CategoryRepository $categoryRepository
    )
    {
//        UrlGeneratorInterface $urlGenerator

        $category = $categoryRepository->findOneBy([
            'slug' => $category_slug
        ]);

        $product = $productRepository->findOneBy([
            'slug' => $slug,
            'category' => $category
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);

//        return $this->render('product/show.html.twig', [
//            'product' => $product,
//            'urlGenerator' => $urlGenerator
//        ]);
    }

    /**
     * @Route("/admin/product/create",name="product_create")
     */
    public function create(Request $request,
                           SluggerInterface $slugger,
                            EntityManagerInterface $entityManager)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

//          $product = $form->getData(); inutile si on passe un new product dans le createform.
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $entityManager->persist($product);
            $entityManager->flush();
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);

    }

    /**
     * @Route("/admin/product/{id}/edit",name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository,
                         Request $request,
                         EntityManagerInterface $entityManager,
                         UrlGeneratorInterface $urlGenerator)
    {

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
//        $form->setData($product); on enlève car le $product est passé dans le createform

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // $product = $form->getData();
            // est inutile, car le formulaire travaille directement sur l'objet product

//            $product->setSlug(strtolower($slugger->slug($product->getName())));
            // Apparement, il ne faut pas changer le slug, il reste permaent au cas ou qqn acéderait directement au produit via l'url.
            // Pour moi c'est discutable ...

            $entityManager->flush();

//            $url = $urlGenerator->generate('product_show', [
//                'category_slug' => $product->getCategory()->getSlug(),
//                'slug' => $product->getSlug()
//            ]);
//            return $this->redirect($url);
                    // Ou bien :
//            return new RedirectResponse($url);

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);

    }
}
