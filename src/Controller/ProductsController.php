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
        $kot = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('name', TextType::class, ['label'=>'Name:'])
            ->add('price', NumberType::class, ['label'=>'Price:'])
            ->add('send', SubmitType::class, ['label'=>'Show the chosen'])
            //->add('sendall', SubmitType::class, ['label'=>'Show all items'])
            ->getForm();

        $kot->handleRequest($request);

        if ($kot->isSubmitted()) {
            
            $data = $kot->getData();
            $price=$data['price'];
            $name=$data['name'];
            
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findBy(['price' => $price,
                        'name' => $name
                        ]);
        }
        else 
        {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findAll();
            
        } 

        $contents = $this->renderView('products/index.html.twig',
            [
                'kot' => $kot->createView(),
                'products' => $products,
            ],
        );
        
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