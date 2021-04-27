<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Entity\OrderProduct;

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

        //$cartArray = array();
        $arrayOfOrderProductsObjectsInCart = array();
        
        foreach ($all as $key=>$value) {
            
            if (is_integer($key)) {
                
                $orderProduct = new OrderProduct();

                $orderProduct->setAmount($value);

                $product=$this->getDoctrine()->getRepository(Product::class)->find($key);
                $orderProduct->setProducts($product);

                array_push($arrayOfOrderProductsObjectsInCart, $orderProduct);

            }
        }
                
        $contents = $this->renderView('cart_view/cart_view.html.twig',
                [
                    'arrayOfOrderProductsObjectsInCart' => $arrayOfOrderProductsObjectsInCart,
                ],
            );
        return new Response($contents);
        
    }

    
}
