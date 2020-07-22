<?php
// src/Controller/ProductsController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

class ProductsController extends AbstractController
{
    public function products()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
             
        $contents = $this->renderView('products/index.html.twig',
       
            [
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