<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    // Attention, cette fonction n'est pas une route, du coup, on ne peut pas y injecter des services comme on le fait pour une route.
    // Il faut passer par un constructeur.
    public function renderMenuList()
    {

        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $entityManager,
                           SluggerInterface $slugger,
                           Request $request): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $formView = $form->createView();

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('homepage');
        }

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }


    /**
     * @Route("/admin/category/{id}/edit", name="category-edit")
     */
    public function edit(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security): Response
    {
        $user = $security->getUser();

        if($user === null) {
            return $this->redirectToRoute('security_login');
        }

        if (!in_array("ROLE_ADMIN",$user->getRoles())) {
            throw new AccessDeniedHttpException("Vous n'avez pas le droit à cette ressource");
        }
        $category = $categoryRepository->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
