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

class ProductsController extends AbstractController
{
    public function products (Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('SEND', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            
            $data = $form->getData();
            $price=$data['price'];
            $name=$data['name'];
            
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findBy(['price' => $price,
                        'name' => $name
                        ] );
                
            $contents = $this->renderView('products/index.html.twig',
                [
                    'form' => $form->createView(),
                    'products' => $products,
                ],
            
            );
        }
        else 
        {
            $contents = $this->renderView('products/index.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                );
        }
        return new Response($contents);
    }

    public function product($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
               
        $contents = $this->renderView('product/index.html.twig',
       
            [
                'product' => $product,
            ],
        );

        return new Response($contents);
    }
}