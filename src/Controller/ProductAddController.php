<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
               
        $form = $this->createForm (ProductType::class, $product);
         
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