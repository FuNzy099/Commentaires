<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(ProductRepository $productRepository): Response
    {

        /*
            Permet de récuperer l'enssemble des produits avec la methode native findAll.
            Je n'ai pas besoin de mettre de paramettre, car je dispose dans la base de données que de trois produits
        */ 
        $products = $productRepository ->findAll([]);

    
        return $this->render('home/index.html.twig', [

            'products' => $products

        ]);
    }
}
