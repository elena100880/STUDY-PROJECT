<?php
// src/Controller/RegistController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RegistController extends AbstractController
{

    public function registration (Request $request)
    {
        //формирует конфиг внутри себя по переданным тобой вещам и возвращает форму
        $defaultData = array ();
        $form = $this->createFormBuilder($defaultData)
            
            ->add('Enter_your_email:', EmailType::class, array (
                'required'=>false,
                'attr'=> array ('class'=>'kot',
                    'placeholder'=>'почта',
                    'onfocus'=>'this.placeholder=""',
                    'onblur'=>'this.placeholder="почта"',
                ),
                ))
            ->add('Enter_your_password:', PasswordType::class)
            ->add('Repeat_password:', PasswordType::class)
            ->add('SEND', SubmitType::class)
            ->add('RESET', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $data = $form->getData();

            if ($data['Enter_your_password:']!=$data['Repeat_password:']) {
                $message='Enter equal passwords';      
                $contents = $this->renderView('registration/index.html.twig',
                    [
                        'data'=> $data,
                        'form' => $form->createView(),
                        'message'=> $message
                    ],
                );
            }
            else {
                $message='OK';      
                $contents = $this->renderView('registration/index.html.twig',
                    [
                        'data'=> $data,
                        'form' => $form->createView(),
                        'message'=> $message
                    ],
                );

            }
        }
        else {
            $message="";
            $contents = $this->renderView('registration/index.html.twig',
                [
                     'form' => $form->createView(),
                    'message'=> $message
                ],
            );
        }

        return new Response($contents);
        
    }
}