<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartViewController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
        
    public function cartView(): Response
    {
        
        
        
        
        $contents = $this->renderView('cart_view/cart_view.html.twig',
                [
                    
                ],
            );
        return new Response($contents);
        
    }
}
