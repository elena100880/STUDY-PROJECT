<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    public function orderForm(): Response
    {
        $contents = $this->renderView('order_form/order_form.html.twig',
                [
                    
                ],
            );
        return new Response($contents);
    }
}
