<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Product;
use App\Entity\OrderProduct;
use App\Entity\Order;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        //preventing showing products in Cart in Order table if SendOrder-button was not already clicked on Cart-page:
        if ($this->session->get('sendOrderClicked') ) {
            $arrayOfOrderProductsInCart = $this->session->get('arrayOfOrderfProductsInCart', null);
        }
        else $arrayOfOrderProductsInCart = 0;

        $form = $this->createFormBuilder()
                                        ->add('name', TextType::class,  [
                                                                            'label'=> 'Name',
                                                                          
                                                                        ])
                                        ->add('surname', TextType::class,  ['label'=> 'Surname'])  
                                        ->add('street', TextType::class,  ['label'=> 'Street, house/apartment'])   
                                        ->add('city', TextType::class,  ['label'=> 'City'])                                                                     
                                        ->add('post', TextType::class,  ['label'=> 'Postal code'])                                    
                                                                               
                                        ->add('send', SubmitType::class, ['label'=>'Complete ORDER'])
                                        ->add('reset', ResetType::class, ['label'=>'RESET'])
                                        ->getForm();
        $form->handleRequest($request);  
        
        if ($form->isSubmitted() ) {

            $time = time();
            $date = new \DateTime();
            $user = $this->getUser();

            $order = new Order();
            $order->setDate($date);
            if ($user) $order->setUser($user);

            //saving Order to DB:
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            //saving OrderProducts to DB:
            foreach ($arrayOfOrderProductsInCart as $orderProduct) {
                
                $orderProduct->setOrders($order);

                $product = $this->getDoctrine()->getRepository(Product::class)->find($orderProduct->getProducts()->getId());
                $orderProduct->setProducts($product);

                $entityManager->persist($orderProduct);
                $entityManager->flush();
            }
            session_unset();
            return $this->redirectToRoute('products');
        }
       
        $contents = $this->renderView('order_form/order_form.html.twig',
                [
                    'arrayOfOrderProductsInCart' => $arrayOfOrderProductsInCart,
                    'form' => $form->createView(),
                ],
            );
        return new Response($contents);
    }
}
