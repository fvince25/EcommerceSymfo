<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use Bluemmb\Faker\PicsumPhotosProvider;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerEcommerce\Ecommerce;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;
    protected $encoder;

    // SluggerInterface vient du package string.
    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder) {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
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

        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, "password");


        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullName("Admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u< 5 ; $u++) {

            $user = new User();
            $hash = $this->encoder->encodePassword($admin, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }

        for($p = 0; $p < mt_rand(20,40) ; $p++) {

            $purchase = new Purchase;

            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000,30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

//                ->setUser($users[mt_rand(0,sizeof($users))]);

            // Avec un argument dans boolean, on indique un random true/false
            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            } else {
                $purchase->setStatus(Purchase::STATUS_PENDING);
            }

            $manager->persist($purchase);
        }


        $manager->flush();
    }
}
