<?php

namespace App\Taxes;

Class Detector {

    protected $seuil;

    public function __construct(Float $seuil)
    {
        $this->seuil = $seuil;
    }

    public function Detect(float $price) : Bool {

        return $price > $this->seuil;

    }

}