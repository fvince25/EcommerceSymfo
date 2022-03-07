<?php


namespace App\EventDispatcher;
use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ProductViewSubscriber implements EventSubscriberInterface
{

    protected $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendViewProductEmail'
        ];
    }


    public function sendViewProductEmail(ProductViewEvent $productViewEvent) {
        $this->logger->info("Email envoyé pour la visualisation du produit ".$productViewEvent->getProduct()->getName());
    }


}