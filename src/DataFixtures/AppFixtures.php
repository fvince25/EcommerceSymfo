<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Bluemmb\Faker\PicsumPhotosProvider;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerEcommerce\Ecommerce;
use Liior\Faker\Prices;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;

    // SluggerInterface vient du package string.
    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Ecommerce($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));

        // Cette façon de faire ne va pas avec SluggerInterface !!!
        // $slugify = new Slugify();


        for ($c = 0; $c < 3; $c++) {

            $category = new Category();
            $category->setName($faker->domainName)
            ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {

                $product = new Product();
                $watch = $faker->watches();

                $product->setName($watch)
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph)
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $manager->persist($product);
            }

        }




        $manager->flush();
    }
}
