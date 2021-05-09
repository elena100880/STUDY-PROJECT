<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Entity\OrderProduct;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
        $arrayOfOrderProductsInCart = array();
        $arrayOfKeys = array();
        $arrayOfValues = array();
        $arrayOfRadios = array();
        foreach ($all as $key=>$value) {
            
            if (is_integer($key)) {
                
                $orderProduct = new OrderProduct();
                $orderProduct->setAmount($value);
                $product=$this->getDoctrine()->getRepository(Product::class)->find($key);
                $orderProduct->setProducts($product);

               
                array_push($arrayOfOrderProductsInCart, $orderProduct); //array of OrderProducts objects in Cart
                array_push($arrayOfKeys, $key);
                array_push($arrayOfValues, $value);
                array_push($arrayOfRadios, false);
                $totalQuantityOfItemsInCart = $totalQuantityOfItemsInCart + $value;
                
            }
        }

        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart);
        $this->session->set('arrayOfOrderfProductsInCart', $arrayOfOrderProductsInCart);

        $form = $this->createFormBuilder()
                                    ->add('amounts', CollectionType::class,  [
                                                                                'label'=> false,
                                                                                'entry_type' => NumberType::class,
                                                                                'allow_add' => true,
                                                                                'allow_delete' => true,
                                                                                'data' => $arrayOfValues,
                                                                                'entry_options' =>  [
                                                                                                        'label'=> false,
                                                                                                        'required' => false,
                                                                                                    ],
                                                                                
                                                                            ])
                                    ->add('delete_products', CollectionType::class,  [
                                                                                    'label'=> false, 
                                                                                    'entry_type' => CheckboxType::class,
                                                                                    'allow_add' => true,
                                                                                    'allow_delete' => true,
                                                                                    'data' => $arrayOfRadios,
                                                                                    'entry_options' =>  [
                                                                                                            'label'=> false,
                                                                                                            'required' => false,
                                                                                                        ],
                                                                                    
                                                                            ])

                                    ->add('send', SubmitType::class, ['label'=>'Make ORDER'])
                                    ->add('refresh', SubmitType::class, ['label'=>'RE-COUNT your Cart'])
                                    ->add('reset', ResetType::class, ['label'=>'RESET'])
                                    ->getForm();

        $form->handleRequest($request);                            
                                    
        if ($form->isSubmitted() ) {
                
            if ($form->get('send')->isClicked() ) {
                
                
                return $this->redirectToRoute('order_form');

            }
            else {

                $data = $form->getData();
                $amounts = $form->get('amounts')->getData(); //array of ammounts from each product in the table

                $i=0;
                foreach ($amounts as $amount) {

                    $this->session->set($arrayOfKeys[$i], $amount);
                    $i++;
                   
                }
                return $this->redirect($request->getUri());  
            }
            
        }
                
        $contents = $this->renderView('cart_view/cart_view.html.twig',
                [
                    'arrayOfOrderProductsInCart' => $arrayOfOrderProductsInCart,
                    'form' => $form->createView(),
                ],
            );
        return new Response($contents);
        
    }

    
}
