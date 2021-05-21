<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Entity\OrderProduct;
use App\Entity\Order;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
        
    public function orderForm (Request $request): Response
    {
        /* 
        * not showing products in Order-table on Order-page if SendOrder-button was not clicked on Cart-page yet,
        * or if cart-page was recounted with new amounts,
        * or if some product was added to Cart from product-page: 
        */
        if ($this->session->get('sendOrderClicked') and ($this->session->get('arrayOfProductsInCart')) ) {    
            $arrayOfProductsInCart = $this->session->get('arrayOfProductsInCart');
            $note=null; //flag for empty order after changings in Cart
        }
        else {
            $arrayOfProductsInCart = [];
            $note = 'Your cart was changed/empty or not submitted!! Please go back to the Cart.'; //flag for not empty order after refreshing Cart
        }

        $form = $this->createFormBuilder()
                                        ->add('name', TextType::class,  ['label'=> 'Name'])
                                        ->add('surname', TextType::class,  ['label'=> 'Surname'])  
                                        ->add('street', TextType::class,  ['label'=> 'Street, house/apartment'])   
                                        ->add('city', TextType::class,  ['label'=> 'City'])                                                                     
                                        ->add('post', TextType::class,  ['label'=> 'Postal code'])                                    
                                                                               
                                        ->add('send', SubmitType::class, ['label'=>'Send ORDER'])
                                        //->add('reset', ResetType::class, ['label'=>'RESET'])
                                        ->getForm();
        $form->handleRequest($request); 

        

        if ($form->isSubmitted() ) {
            
        /*    $i = $form->getClickedButton()->getName();

            if ( $form->getClickedButton()->getName() == 'reset' )  {
                return $this->redirectToRoute('products');
            }  */
            
                   
            if (empty($arrayOfProductsInCart) ) {
                    $note = 'Your cart was changed/empty or not submitted!! Please go back to the Cart.';
            }
            else {

                    //saving Order to DB:
                    $date = new \DateTime();
                    $user = $this->getUser();
    
                    $order = new Order();
                    $order->setDate($date);
                    if ($user) $order->setUser($user);
                        
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($order);
                    $entityManager->flush();

                    //saving OrderProducts to DB:
                    $i = 0;
                    foreach ($arrayOfProductsInCart as $product) {
                        
                        $orderProduct = new OrderProduct();

                        $orderProduct->setOrders($order);

                        $arrayOfAmounts = $this->session->get('arrayOfAmounts');
                        $orderProduct->setAmount( $arrayOfAmounts[$i] );

                        $productToOrderProduct = $this->getDoctrine()->getRepository(Product::class)->find($product->getId());
                        $orderProduct->setProducts($productToOrderProduct);

                        $entityManager->persist($orderProduct);
                        $entityManager->flush();
                        $i++;
                    }
                
                    //remowing session variables:
                    /**
                     * @todo remove session variables all at once (by session_unset() for example), but not logging out??:
                     */
                    
                    $this->session->remove('arrayOfProductsInCart');
                    $this->session->remove('arrayOfAmounts');
                    $this->session->remove('totalQuantity');
                    $this->session->remove('sendOrderClicked');
                    $all=$this->session->all();
                    foreach ($all as $key=>$value) {
                    
                        if (is_integer($key)) { 
                            $this->session->remove($key);         
                        }
                    }
                    return $this->redirectToRoute('products');
            }
        }
       
        $contents = $this->renderView('order_form/order_form.html.twig',
                [
                    'arrayOfProductsInCart' => $arrayOfProductsInCart,
                    'form' => $form->createView(),
                    'note' => $note,
                ],
            );
        return new Response($contents);
    }
}
