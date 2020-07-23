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

class ProductAddController extends AbstractController
{
    public function productadd (Request $request)
    {
        $product = new Product();
        $product->setName('sandals');
        $product->setPrice('50');
        
        
        $form = $this->createFormBuilder()
            ->add('name', TextType::class)
            ->add('price', NumberType::class)
            ->add('SEND', SubmitType::class, ['label' => 'SEND'])
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
                
            $contents = $this->renderView('productadd/index.html.twig',
                [
                    'form' => $form->createView(),
                    'product' => $product,
                ],
            
            );
        }
        else 
        {
            $contents = $this->renderView('productadd/index.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                );
        }
        return new Response($contents);
    }

   
}