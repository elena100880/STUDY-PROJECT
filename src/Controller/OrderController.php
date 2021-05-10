<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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

        $form = $this->createFormBuilder()
                                        ->add('name', TextType::class,  ['label'=> 'Name'])
                                        ->add('surname', TextType::class,  ['label'=> 'Surname'])  
                                        ->add('street', TextType::class,  ['label'=> 'Street, house/apartment'])   
                                        ->add('city', TextType::class,  ['label'=> 'City'])                                                                     
                                        ->add('post', TextType::class,  ['label'=> 'Postal code'])                                    
                                                                               
                                        ->add('send', SubmitType::class, ['label'=>'Make ORDER'])
                                        ->add('reset', ResetType::class, ['label'=>'RESET'])
                                        ->getForm();
        
        if ($form->isSubmitted() ) {

            
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
