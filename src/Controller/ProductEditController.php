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

class ProductEditController extends AbstractController
{
    public function productedit (Request $request, $id)
    {
        $productManager = $this->getDoctrine()->getManager();
        $product = $productManager->getRepository(Product::class)->find($id);

        $id=$product->getId();
        $name1=$product->getName();
        $price1=$product->getPrice();
        $description1=$product->getDescription();

        $form = $this->createForm (ProductType::class, $product);
        $form->handleRequest($request);
                  
        if ($form->isSubmitted()) {
            $save='saved';
            $productManager->flush();
                                      
            $contents = $this->renderView('productedit/index.html.twig',
                [
                    'form' => $form->createView(),
                    'id'=> $id,
                    'name1'=> $name1,
                    'price1'=> $price1,
                    'description1'=> $description1,
                    'product' => $product,
                    'save'=>$save,
                ],
            );
        }
        else 
        {
            $contents = $this->renderView('productedit/index.html.twig',
                    [
                        'form' => $form->createView(),
                        'id'=> $id,
                        'name1'=> $name1,
                        'price1'=> $price1,
                        'description1'=> $description1,
                        'product' => $product,
                    ],
                );
        }
        return new Response($contents);
    }

   
}