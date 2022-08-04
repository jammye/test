<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setPrice(10)
            ->setStock(5)
            ->setTax(20)
            ->setTitle("Climatisation #1")
            ->setPrice(329);
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(20)
            ->setStock(10)
            ->setTax(20)
            ->setTitle("Climatisation #2");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(30)
            ->setStock(0)
            ->setTax(20)
            ->setTitle("Climatisation #3");
        $manager->persist($product);

        $product = new Product();
        $product->setPrice(40)
            ->setStock(1)
            ->setTax(20)
            ->setTitle("Climatisation #4");
        $manager->persist($product);

        $manager->flush();
    }
}
