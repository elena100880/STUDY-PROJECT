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
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        //flag for deleting data in Order-table on Order-page after opening again or refreshing Cart-page:
        $this->session->remove('sendOrderClicked');

        $all=$this->session->all();

        $totalQuantityOfItemsInCart = 0;
        $arrayOfOrderProductsInCart = array();  //array of OrderProducts objects in Cart
        $arrayOfIds = array();   //array of IDs of products in Cart
        $arrayOfAmounts = array();   //array of amounts of products in CART
        $arrayOfRadios = array();   //array of 'Falses' for 'data' option for checkboxes in CollectionType

        $i=0;

        foreach ($all as $key=>$value) {
            
            /**
             * @todo mayby another way of extracting only 'products session variables'?, some filter??
             */
            if (is_integer($key)) { 
                             
                $orderProduct = new OrderProduct();
                $orderProduct->setAmount($value);
                $product=$this->getDoctrine()->getRepository(Product::class)->find($key);
                $orderProduct->setProducts($product);
               
                array_push($arrayOfOrderProductsInCart, $orderProduct); 
                array_push($arrayOfIds, $key); 
                array_push($arrayOfAmounts, $value);
                array_push($arrayOfRadios, false);
                $totalQuantityOfItemsInCart = $totalQuantityOfItemsInCart + $value;
                
            }
        }
        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart);
        $this->session->set('arrayOfOrderfProductsInCart', $arrayOfOrderProductsInCart);
                
        /**
         * @todo 
         * if possible - make custom formType class or FormType for OrderProduct class
         * instead of the below createFormBuilder in controller:
         */
        $form = $this->createFormBuilder()
                                    ->add('amounts', CollectionType::class,  [
                                                                                'label'=> false,
                                                                                'entry_type' => TextType::class,
                                                                                'allow_add' => true,
                                                                                'allow_delete' => true,
                                                                                'data' => $arrayOfAmounts,
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
                                    ->add('recount', SubmitType::class, ['label'=>'Recount Cart'])
                                    ->add('reset', ResetType::class, ['label'=>'RESET'])
                                    ->getForm();

        $form->handleRequest($request);                            
                                    
        if ($form->isSubmitted() ) {
                
            if ($form->get('send')->isClicked() ) {
                
                $this->session->set('sendOrderClicked', true); ///flag for not deleting data in Order-table on Order-page after making order
                return $this->redirectToRoute('order_form');
            }
            else {

                $data = $form->getData();
                $amounts = $form->get('amounts')->getData(); //array of ammounts from each product in the table
                $delete_products = $form->get('delete_products')->getData();

                $i=0;
                foreach ($amounts as $amount) {

                        //deleting the whole product from Cart if checkbox is marked or amount-field is 0 or empty:
                        if (isset($delete_products[$i]) or $amount == 0 or $amount == null) {

                            /**
                             * @todo
                             * not able just to redirect to Route 'delete_whole_product_from_cart'
                             * because then -  not deleting more than 1 whole product from the Cart at once
                             */

                            $id_incart = $arrayOfIds[$i];
                            $removedQuantity = $this->session->get($id_incart);
                            $this->session->remove($id_incart);
                            $this->session->remove('note'.$arrayOfIds[$i]);

                            $totalQuantityOfItemsInCart = $this->session->get('totalQuantity');
                            $this->session->set('totalQuantity', $totalQuantityOfItemsInCart - $removedQuantity);
                            
                        }

                        //validating and changing the amount of product:
                        /**
                         * @todo
                         * make validation in custom FormType, if I'll make that custom FormType in future ?? 
                         */
                        else {
                            if (is_numeric($amount) and ($amount - floor( $amount) == 0 ) ) {
                                $this->session->set($arrayOfIds[$i], $amount);
                                $this->session->remove('note'.$arrayOfIds[$i]);
                            }
                            else {
                                $note = "Please enter the whole number for ID $arrayOfIds[$i] instead of '$amount' !!";
                                $this->session->set('note'.$arrayOfIds[$i], $note);
                            }
                        }
                                    
                    $i++;
                }
                return $this->redirectToRoute('cart_view'); 
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
