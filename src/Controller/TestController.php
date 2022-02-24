<?php
namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController {

    public function index() {
        dd("ca fonctionne");

    }

    /**
     * @param Request $request
     * @param $age
     * @return Response
     * @Route("/test/{age<\d+>?0}", name="test")
     */
    public function test(Request $request, $age) {

        // (argument resolver)

//        $age = $request->attributes->get('age',0);

        return new Response("Vous avez $age ans");
    }

    /**
     * @param Request $request
     * @param $age
     * @return Response
     * @Route("/test2/{age}", name="test2")
     */
    public function test2(Request $request, $age) {

        // (argument resolver)

//        $age = $request->attributes->get('age',0);

        return new Response("Vous avez $age ans");
    }
}

