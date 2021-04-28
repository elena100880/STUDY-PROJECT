<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Entity\OrderProduct;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

        $totalQuantityOfItemsInCart = 0;
        $arrayOfOrderProductsObjectsInCart = array();
        foreach ($all as $key=>$value) {
            
            if (is_integer($key)) {
                
                $orderProduct = new OrderProduct();
                $orderProduct->setAmount($value);
                $product=$this->getDoctrine()->getRepository(Product::class)->find($key);
                $orderProduct->setProducts($product);

                $v = $orderProduct->getTotalValue();

                array_push($arrayOfOrderProductsObjectsInCart, $orderProduct); //array of OrderProducts objects in Cart

                $totalQuantityOfItemsInCart = $totalQuantityOfItemsInCart + $value;
                
            }
        }

        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart);

        $form = $this->createFormBuilder()
                                    ->add('amount', NumberType::class)
                                    ->add('delete_product', CheckboxType::class)

                                    ->add('send', SubmitType::class, ['label'=>'Make ORDER'])
                                    ->add('refresh', SubmitType::class, ['label'=>'REFRESH your Cart'])
                                    ->getForm();

        $form->handleRequest($request);                            
                                    
        if ($form->isSubmitted() ) {
                
            if ($form->get('send')->isClicked() ) {
                
                return $this->redirectToRoute('order_form');

            }
            else {
                
                $data = $form->getData();
                //$amount=$data[''];   
            }
        }
                
        $contents = $this->renderView('cart_view/cart_view.html.twig',
                [
                    'arrayOfOrderProductsObjectsInCart' => $arrayOfOrderProductsObjectsInCart,
                    'form' => $form->createView(),
                ],
            );
        return new Response($contents);
        
    }

    
}
