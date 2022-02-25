<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator {

    protected $logger;
    protected $tva;


    public function __construct(LoggerInterface $logger, float $tva) {
        // Dans le services.yaml, on a indiqué que le paramètre inconnu tva (qui ne peut pas être automatiquement injecté)
        // sera valorisé à 20.
        $this->logger = $logger;
        $this->tva = $tva;
    }

    public function calcul(float $prix) : float {
        return $prix * ($this->tva/100);
    }
}