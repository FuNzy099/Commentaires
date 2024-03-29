<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        
        $this -> slugger = $slugger;
        
    }
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $faker -> addProvider(new \Liior\Faker\Prices($faker));
        $faker -> addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker -> addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

  
        for( $p = 0; $p < 3; $p++){

            $product = new Product;

            $product -> setName($faker -> productName())
                -> setPrice($faker -> price(4000, 20000))
                -> setSlug(strtolower($this -> slugger -> slug($product->getName())))
                -> setMainPicture($faker -> imageUrl(400,400, true))
                -> setDescription($faker -> paragraph(25)); 
        
            $manager->persist($product);
                
        }

        $manager->flush();
    }
}
