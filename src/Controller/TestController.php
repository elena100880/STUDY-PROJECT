<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

class TestController extends AbstractController
{
    public function test (Request $request)
    {
        $form = $this->createFormBuilder()
            
            ->add('price', NumberType::class)
            ->add('SEND', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $data = $form->getData();
            $price=$data['price'];
            
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findBy (['price' => $price ]);
                      
            $contents = $this->renderView('test/test.html.twig',
                [
                    'form' => $form->createView(),
                    'product' => $product,
                ],
            );
        }
        else {
           
            $contents = $this->renderView('test/test.html.twig',
                [
                    'form' => $form->createView(),
                ],
            );
        }
        return new Response($contents);
    }
}