<?php

namespace App\EventListener;
use Symfony\Contracts\EventDispatcher\Event;
use App\Entity\Product;

class ProductAddListener
{
    public function onProductEvent(Event $event): void
    {
        $product_data = $event->getProductData();
    }
}
