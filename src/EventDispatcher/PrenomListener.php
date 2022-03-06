<?php

namespace App\EventDispatcher;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomListener {

    public function addPrenomToAttributes(RequestEvent $requestEvent) {
        $requestEvent->getRequest()->attributes->set('prenom', 'Vincent');
    }
}

?>