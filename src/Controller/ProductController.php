<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Data\SearchData;
use App\Form\OpinionType;
use App\Form\SearchType;
use App\Repository\OpinionRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository, OpinionRepository $opinionRepository, Request $request, ManagerRegistry $doctrine): Response
    {

        // Instanciation d'un nouvel objet Opinion
        $opinion = new Opinion;

        // Permet de recuperer le produit correspond au slug dans l'url
        $product = $productRepository -> findOneBy(['slug' => $slug]);

        $data = new SearchData;

        $data -> page = $request->get('page', 1);

        /*
            Permet de recuperer les avis du produit en question,

            la methode search est presente dans OpinionRepository.
        */
        $opinions = $opinionRepository -> search($data, $slug);

        // Condition verifiant si le produit demandé existe.
        if(!$product){

            /*
                throw permet de lancer une exception,
                createNotFounsException nous vient de la class AbstractController permettant d'afficher une erreur indiquant que la page n'existe pas (erreur 404)
            */
            throw $this -> createNotFoundException("Le produit n'existe pas ! ");
        }

        // Creation du formulaire d'avis qui ce base sur OpinionType
        $form = $this->createForm(OpinionType::class, $opinion);

        // Permet d'analyser la requette
        $form -> handleRequest($request);

        // Condition permettant de verifier si le formulaire est soumit et si les données saisi sont valides
        if($form -> isSubmitted() && $form -> isValid()){
            
            /*
                $newPicture me permet de récuperer l'image que l'utilisateur à renseigné,
                Cette variable me sera utile notament pour faire une condition verifiant si l'utilisateur à saisi une image ou non !
            */ 
            $newPicture = $form -> get('picture') -> getData();

            // Je récupére l'enssemble des informations du formulaire
            $opinion = $form -> getData();

            // Condition me permettant de verifier l'existance d'une image
            if(isset($newPicture)){

                /*
                    Condition permettant de vérifier si l'extenssion de l'image est dans un format autorisé

                    in_array => Permet d'idiquer si une valeur appartient au tableau

                    DOCUMENTATION : 
                        in_array : https://www.php.net/manual/fr/function.in-array.php
                */
                if(in_array($newPicture -> guessExtension(), ["png", "jpeg", "jpg", "webp"])){
                 
                    // Cette condition permet de définir un poid maximum autorisé pour l'avatar, ici à l'occurence 2Mo max !
                    if($newPicture -> getSize() <= 2000000){

                        /*
                            Je hache et applique un identifiant unique au fichier

                            md5     => C'est un algorithme de hachage faible
                            uniqId  => Permet de génèrer un identifiant unique basé sur la date et l'heure courante

                            DOCUMENTATION: 
                                    md5 : https://www.php.net/manual/fr/function.md5.php
                                    uniqid : https://www.php.net/manual/fr/function.uniqid.php
                        */ 
                        $namePicture = md5(uniqid()) . '.' .$newPicture -> guessExtension();

                        /*
                            Je déplace le fichier de l'utilisateur dans un répértoire dédié aux uploads (public/img/picturesUpload)

                            Pour ce faire j'ai définit un parameters dans le fichier config/service.yaml
                        */ 
                        $newPicture -> move(
                            $this -> getParameter('pictureUpload_directory'),
                            $namePicture
                        );
                    }

                }
                
                $entityManager = $doctrine -> getManager();

                // j'utilise setPicture me permettant definir le nom du fichier
                $opinion -> setPicture($namePicture);

                // J'utilise setProduct me permettant de définir le produit qui est lié à l'avis de l'utilisateur
                $opinion -> setProduct($product);
            
                // On persit() notre user, c'est l'équivalent de prepare() en PDO
                $entityManager -> persist($opinion);

                $entityManager -> flush();

            // Si il n'y a pas d'image dans le formulaire
            } else {

                $opinion -> setProduct($product);

                $entityManager = $doctrine -> getManager();
    
                $entityManager -> persist($opinion);
    
                $entityManager -> flush();

            }

            return $this -> redirectToRoute('product_show', ['slug' => $product -> getSlug()]);
        }

        return $this->render('product/show.html.twig', [
            'slug' => $slug,
            'product' => $product,
            'opinions' => $opinions,
            'formAddOpinion' => $form->createView()
        ]);
    }
}
