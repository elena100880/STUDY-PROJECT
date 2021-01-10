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
            ->add('name', TextType::class, ['label'=>'Name:',
                                            'required' => false])
            ->add('price_min', NumberType::class, ['label'=>'Price from:',
                                                    'required' => false])
            ->add('price_max', NumberType::class, ['label'=>'Price to:',
                                                    'required' => false])
            ->add ('category', EntityType::class, [
                'class'=> Category::class,
                'choice_label' => 'name',
                'label' => 'Choose category:',
                'multiple' => true, 
                'required' => false,
                //'attr' => array('placeholder' => '') 
                ])
            ->add('send', SubmitType::class, ['label'=>'Show the chosen'])
            ->getForm();

        $kot->handleRequest($request);

        if ($kot->isSubmitted()) {
            
            $data = $kot->getData();
            $price_min=$data['price_min'];
            $price_max=$data['price_max'];
            $name=$data['name'];
            $categories=$data['category'];
           
            function getAllId ($categories) {
                
                $all_id= Array();

                foreach ($categories as $cat) {
                   
                    array_push ($all_id, $cat->getId() );
                    $childs=$cat->getChildCategories(); 
                                            
                    $all_id=array_merge ($all_id, getAllId($childs));
                   
                }             
                return $all_id;
            } 
    
            $all_id=getAllId ($categories);

            //$qb = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder('p')
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder()
                        -> select('p, c')
                        -> from ('App\Entity\Product', 'p')
                        -> join ('p.category', 'c')
                        -> orderBy('p.price', 'DESC');

                if (isset($name)) {
                    $qb= $qb-> setParameter('name', $name)
                            -> andWhere('p.name = :name');
                }            
                if (isset($price_max)) {
                    $qb= $qb-> setParameter('price_max', $price_max)
                            -> andWhere('p.price <= :price_max');
                }        
                if (isset($price_min)) {
                    $qb= $qb-> setParameter('price_min', $price_min)
                            -> andWhere('p.price >=:price_min');
                }  
                if (!empty($all_id)) {
                    $qb= $qb-> setParameter('all_id', $all_id)
                            -> andWhere('c.id in (:all_id)');
                }              
                
                $dql=$qb->getDQL();

                $q=$qb->getQuery();
                $sql=$q->getSQL();
                $param=$q->getParameters();

               
                //$param=$q->getParameters();
                $products = $qb->getQuery() -> getResult();



            /* my first queries to database from Form:
                    
                    $products = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->findBy(['price' => $price,
                            'name' => $name,
                            ]);
            */
            
            /*    
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
            */

            $contents = $this->renderView('products/products.html.twig',
                [
                    'kot' => $kot->createView(),
                    'products' => $products,
                    'sql' => $sql,
                    'dql' => $dql,
                    'param' => $param
                    //'category' =>$category,
                ],
            );               
        }
        else 
        {
            $products = $this->getDoctrine()
                ->getRepository(Product::class)
                ->findAll();
           
            $contents = $this->renderView('products/products.html.twig',
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
               
        $contents = $this->renderView('product/product.html.twig',
            [
                'product' => $product,
            ],
        );

        return new Response($contents);
    }
}