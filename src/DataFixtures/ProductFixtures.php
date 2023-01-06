<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 20 products!
        for ($i = 1; $i <= 10; $i++) {
            $product = new Product();
            $product->setName('product '.$i);
            $product->setDescription('description '.$i);
            $product->setPrice(mt_rand(10, 100));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
