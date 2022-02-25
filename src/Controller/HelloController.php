<?php

namespace App\Controller;


use App\Taxes\Detector;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class HelloController {

    protected $logger;
    protected $calculator;

    public function __construct(LoggerInterface $logger, Calculator $calculator) {

        $this->logger = $logger;
        $this->calculator = $calculator;
    }
    /**
     * @Route("/hello/{prenom?World}", name="hello", methods={"GET","POST"})
     */
    public function hello($prenom, Slugify $slugyfy, Detector $detector) {

        // Comme Slugyfy vient d'une bibliothèque tierce (téléchargée de packagist (composer require cocur/slugify)) ,
        // ça ne fonctionnera que s'il est déclaré dans services.yaml
        // Et comme ça on se le fait livrer ;-)

        // Détector est livré par injection de dépendance dans la fonction
        dump($detector->Detect(102));
        dump($detector->Detect(10));

        dump($slugyfy->slugify("Hello World"));

        // calculator est livré par injection de dépendance dans le constructeur. (au choix)
        dump($this->calculator->calcul(100));

        $this->logger->error("Mon Message de Log !");
        return new Response("hello $prenom");

    }

    /**
     * @Route("/hello2/{prenom}", name="hello2", methods={"GET","POST"})
     */
    public function hello2(Environment $twig, $prenom = "world") {

        $html = $twig->render(
            'hello.html.twig',
            [
                'formateur' => [
                    'prenom' => 'Lior',
                    'nom' => 'Chamla',
                    'age' => 33
                ]
            ]
        );
        return new Response($html);

    }

}