<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['Mechanical Keyboard', 8999, 25],
            ['Wireless Mouse', 2999, 50],
            ['USB-C Hub', 4499, 15],
        ];

        foreach ($products as [$name, $price, $stock]) {
            $product = new Product();
            $product
                ->setName($name)
                ->setDescription("$name for everyday use.")
                ->setPrice($price)
                ->setStock($stock)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());

            $manager->persist($product);
        }

        $manager->flush();
    }
}
