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
                'r_str' => ['odin'=>'ala', 'dwa'=>'ma', 'tree'=>'kot', 'four'=>'pies'],
                'www'=>$a
            ],

        
        );

        return new Response($contents);
    }
}