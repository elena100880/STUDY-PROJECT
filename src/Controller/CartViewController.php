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
        $all=$this->session->all();

        $totalQuantityOfItemsInCart = 0;
        $arrayOfOrderProductsInCart = array();
        $arrayOfIds = array();
        $arrayOfAmounts = array();
        $arrayOfRadios = array();

        $i=0;

        foreach ($all as $key=>$value) {
            

            if (is_integer($key)) { 
                /**
                 * @todo mayby another way of extracting only 'products session variables'??
                 */
                
                $orderProduct = new OrderProduct();
                $orderProduct->setAmount($value);
                $product=$this->getDoctrine()->getRepository(Product::class)->find($key);
                $orderProduct->setProducts($product);

               
                array_push($arrayOfOrderProductsInCart, $orderProduct); //array of OrderProducts objects in Cart
                array_push($arrayOfIds, $key);  //array of IDs of products in Cart
                array_push($arrayOfAmounts, $value);   //array of amounts of products in CART
                array_push($arrayOfRadios, false);   //array of 'Falses' for 'data' option for checkboxes
                $totalQuantityOfItemsInCart = $totalQuantityOfItemsInCart + $value;
                
            }
        }

        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart);
        $this->session->set('arrayOfOrderfProductsInCart', $arrayOfOrderProductsInCart);

        /**
         * @todo 
         * make custom formType class instead of the below createFormBuilder in controller??:
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
                $delete_products = $form->get('delete_products')->getData();

                $i=0;
                foreach ($amounts as $amount) {

                    //checking if Product with this id was not deleted earlier fron cart at Product page:
                    //if ( isset($arrayOfIds[$i]) ) {

                        //deleting the whole product from Cart if checkbox is marked or amount is 0 or empty:
                        if (isset($delete_products[$i]) or $amount == 0 or $amount == null) {

                            /**
                             * @todo
                             * not able just to redirect to Route 'delete_whole_product_from_cart'
                             * because then -  not deleting more than 1 more whole product from the Cart at once
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
                
                    /*else {
                        $this->session->remove('note'.$i);
                    } */

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
