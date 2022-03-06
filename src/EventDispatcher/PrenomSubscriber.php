<?php

namespace App\EventDispatcher;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface {

    // Plus besoin d'écrire la configuration dans le service.yaml, on le fait directement dans le PHP,
    // grâce à EventSubscriberInterface.
    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            'kernel.request' => 'addPrenomToAttributes',
            'kernel.controller' => 'test1',
            'kernel.response' => 'test2',
        ];
    }

    public function addPrenomToAttributes(RequestEvent $requestEvent) {
        $requestEvent->getRequest()->attributes->set('prenom', 'Vincent');
    }

    public function test1() {
        dump("test 1");
    }

    public function test2() {
        dump("test 2");
    }
}

?>