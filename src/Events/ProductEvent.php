<?php
namespace App\Events;
 
use Symfony\Contracts\EventDispatcher\Event;
class ProductEvent extends Event 
{
    const NAME = 'product.event';
 
    protected $product_data;
 
    public function __construct($event_data)
    {
        $this->product_data = $event_data;
    }
 
    public function getProductData()
    {
        return $this->product_data;
    }
}