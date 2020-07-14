<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function index()
    {
        
        $a=32;
       
        $contents = $this->renderView('product/index.html.twig',
       
            [
                'random_strings' => ['ala', 'ma','kot', 'pies'],
                'www'=>$a
            ],

        
        );

        return new Response($contents);
    }
}