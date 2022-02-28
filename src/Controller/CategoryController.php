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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
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
        EntityManagerInterface $entityManager): Response
    {
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
