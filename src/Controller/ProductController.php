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
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
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
                         EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        // Validadtion d'une collection complexe :

        $client = [
            'nom' => '',
            'prenom' => 'Lior',
            'voiture' => [
                'marque' => '',
                'couleur' => 'Noir'
            ]
        ];

        $collection = new Collection([
            'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
            'prenom' => [
                new NotBlank(['message' => "Le prénom ne doit pas être vide"]),
                new Length([
                    'min' => 3,
                    'minMessage' => "Le prénom de doit pas fiare moins de 3 caractères"
                ])
            ],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => 'La marque de la voiture est onligatoire']),
                'couleur' => new NotBlank([
                    'message' => 'La couleur de la voiture est obligatoire'
                ])
            ])
        ]);

        $resultat = $validator->validate($client, $collection);

        if ($resultat->count() > 0) {
            dump("il y a des erreurs");
            dd($resultat);
        } else {
            dd("Pas d'erreurs");
        }



        // Validation d'un scalaire simple :

        $age = 200;
        $resultat = $validator->validate($age, [
            new LessThanOrEqual([
                'value' => 120,
                'message' => "L'âge doit être inférieur à {{ compared_value }} mais vous avez donné {{ value }}"
            ]),
            new GreaterThanOrEqual([
                'value' => 0,
                'message' => "L'age doit être supérieur à 0"
            ])
        ]);

        if ($resultat->count() > 0) {
            dd("il y a des erreurs");
        } else {
            dd("Pas d'erreurs");
        }

        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $entityManager->flush();
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
