<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    public function create(FormFactoryInterface $factory, Request $request, ProductRepository $productRepository)
    {

//        CategoryRepository $categoryRepository
//        utile seulement si on passe par les catégories livrées dans la façon artisanale
//        $categories = $categoryRepository->findAll();
//        $options = [];
//
//        foreach ($categories as $category) {
//            $options[$category->getName()] = $category->getId();
//        }

        $builder = $factory->createBuilder(FormType::class,null,[
            'data_class' => Product::class
        ]);

        $builder->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [
                'placeholder' => 'Tapez le nom du produit']
        ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez la description du produit'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit ',
                'attr' => [
                    'placeholder' => 'Tapez le prix du produit en €'
                ]
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'attr' => [],
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => 'name'

            ]);
        // Dans la dernière façon de faire on prends tout, ce qu'on ne veut pas forcément

        // A moins d'avoir une selection en call back du genre :

        //'choice_label' => function() {Category $category) {
        // return strtoupper($category->getName())
        //}

        // Ou alors on utilise du DQL :::


        // Category::class vient de App/Entity.

        // Façon plus artisanale permet d'avoir la selection exacte :

//        ->add('category', choiceType::class, [
//        'label' => 'Catégorie',
//        'attr' => [],
//        'placeholder' => 'Choisir une catégorie',
//        'choices' => $options
//    ]);

        $form = $builder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $data = $form->getData();

            // Cas où on ne passe pas par 'data_class' => Product::class dans le builder.
//            $product = new Product;
//
//            $product->setName($data['name'])
//                ->setShortDescription($data['shortDescription'])
//                ->setPrice($data['price'])
//                ->setCategory($data['category']);

            dd($product);
        }



        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);

    }
}
