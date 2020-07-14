<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class LuckyController extends AbstractController
{
    public function number()
    {
        
        $number = random_int(0, 100);
       
        $contents = $this->renderView('lucky/index.html.twig',
       
            [
                'random_strings' => ['ala', 'ma','kot', 'pies'],
                'number'=>$number,
            ],

        
        );

        return new Response($contents);
    }
     
   
}