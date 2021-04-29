<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
        
    public function orderForm(): Response
    {
        
        $arrayOfOrderProductsInCart = $this->session->get('arrayOfOrderfProductsInCart', null);
        
        
        $contents = $this->renderView('order_form/order_form.html.twig',
                [
                    'arrayOfOrderProductsInCart' => $arrayOfOrderProductsInCart,
                ],
            );
        return new Response($contents);
    }
}
