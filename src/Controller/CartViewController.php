<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

class CartViewController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
        
    public function cartView(Request $request): Response
    {
        
        $all=$this->session->all();

        $cartArray = array();

        
        foreach ($all as $key=>$value) {
            
            if (is_integer($key)) {
                           
                
                $cartArray[$key]=$value;
            }
        }
                
        $contents = $this->renderView('cart_view/cart_view.html.twig',
                [
                    'cartArray' => $cartArray,  
                ],
            );
        return new Response($contents);
        
    }

    
}
