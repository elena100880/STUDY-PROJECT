<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

class ProductAddController extends AbstractController
{
    public function productadd (Request $request)
    {
        $product = new Product();
               
        $form = $this->createForm (ProductType::class, $product)
            ->add('save', SubmitType::class, ['label'=>'Add the item']);
         
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
           
            $productManager = $this->getDoctrine()->getManager();
            $productManager->persist($product);
            $productManager->flush();
            
          //  $id=$product->getId();
                          
            return $this->redirectToRoute('products');
            
        }
        else 
        {
            $contents = $this->renderView('productadd/index.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                );
            return new Response($contents);
        }
        
    }

   
}