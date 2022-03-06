<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
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
        // TODO: Implement getSubscribedEvents() method.
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];

        // Rendue visible dans
        // php bin/console debug:event-dispatcher purchase.success
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        dump($purchaseSuccessEvent);
        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());

//        [Application] Mar  6 16:24:30 |INFO   | APP    Email envoyé pour la commande n° 108

    }

}