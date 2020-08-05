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
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductsController extends AbstractController
{
    public function products (Request $request)
    {
        $kot = $this->createFormBuilder()
            ->setMethod('GET')
            ->add('name', TextType::class, ['label'=>'Name:'])
            ->add('price_min', NumberType::class, ['label'=>'Price from:'])
            ->add('price_max', NumberType::class, ['label'=>'Price to:'])
            ->add ('category', EntityType::class, [
                'class'=> Category::class,
                'choice_label' => 'name',
                'label' => 'Choose category:',
                'multiple' => true,])
            ->add('send', SubmitType::class, ['label'=>'Show the chosen'])
            ->getForm();

        $kot->handleRequest($request);

        if ($kot->isSubmitted()) {
            
            $data = $kot->getData();
            $price_min=$data['price_min'];
            $price_max=$data['price_max'];
            $name=$data['name'];
            $category=$data['category'];

           // $qb = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder()
                        ->select('p')
                        ->from ('App\Entity\Product', 'p')

                        ->where ('p.price >=:price_min AND p.price <= :price_max AND p.name = :name')
                        ->setParameter('price_min', $price_min)
                        ->setParameter('price_max', $price_max)
                        ->setParameter('name', $name)
                        ->orderBy('p.price', 'DESC')
                        ;
            $products = $qb->getQuery()-> getResult();



            /*$products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findBy(['price' => $price,
                        'name' => $name,
                        ]);*/
            
            $productsFilter = Array();
            $i=0;
            foreach ($products as $prod) {
                
                foreach ($category as $cat) {
                    
                    if ($prod ->getCategory()-> getId() == $cat->getId() ) {
                           
                        $productsFilter[$i]=$prod;
                        $i=$i+1;
                    }
                }
            }
            $products=$productsFilter;
            $contents = $this->renderView('products/index.html.twig',
                [
                    'kot' => $kot->createView(),
                    'products' => $products,
                    //'category' =>$category,
                    //'productsFilter' => $productsFilter, 
                ],
            );               
        }
        else 
        {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findAll();
           
            $contents = $this->renderView('products/index.html.twig',
                [
                    'kot' => $kot->createView(),
                    'products' => $products,
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