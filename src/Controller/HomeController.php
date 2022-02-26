<?php
namespace App\Controller;


use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository, EntityManagerInterface $em) {

        // Il n'est pas nécéssaire de se faire livrer le productrepository
        // en injection, on peut créer une instance directement à partir de EntityManagerInterface :
        //$productRepository = $em->getRepository(Product::class);

        // $product = $productRepository->find(3);

//        $product->setPrice(2500);


//        $product = new Product();
//        $product
//            ->setName('Table en métal')
//            ->setPrice(3000)
//            ->setSlug('table-en-metal');

        // Prise en charge, on prépare l'insersion, pour faire tout d'un coups de manière optimisée
        // Le persist ne sert que pour des entités nouvellement créées (pas modifiées).
//        $em->persist($product);

        // Les opérations de suppressions sont gérées dans le entitymanager et non le repository !!!!

        // Préparation de la suppression
        //$em->remove($product);
        //$em->flush();

        //dd($product);

        //$count = $productRepository->count(['price' => 1500]);
        // dump($count);
        return $this->render('home.html.twig');
    }

}