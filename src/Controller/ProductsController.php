<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use App\Entity\Product;
use App\Entity\Category;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductsController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function products (Request $request)
    {
        
        $form = $this->createFormBuilder()
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
                ])
            ->add('send', SubmitType::class, ['label'=>'Show the chosen'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $data = $form->getData();
            $price_min=$data['price_min'];
            $price_max=$data['price_max'];
            $name=$data['name'];
            $categories=$data['category'];
           
        // array of all id of selected categories: 
            function getAllId ($categories) {
                
                $all_id= Array();

                foreach ($categories as $category) {
                   
                    array_push ($all_id, $category->getId() );
                    $childs = $category->getChildCategories();
                                            
                    $all_id=array_merge ($all_id, getAllId($childs));
                   
                }             
                return $all_id;
            } 
            $all_id=getAllId ($categories);

        // another variant: $qb = $this->getDoctrine()->getRepository(Product::class)->createQueryBuilder()->createQueryBuilder('p')
            $entityManager = $this->getDoctrine()->getManager();
            $queryBuilder = $entityManager->createQueryBuilder()
                                            -> select('p, c')
                                            -> from ('App\Entity\Product', 'p')
                                            -> join ('p.category', 'c')
                                            -> orderBy('p.price', 'DESC');

            if (isset($name)) {
                $queryBuilder= $queryBuilder->setParameter('name', $name)
                                            -> andWhere('p.name = :name');
            }            
            if (isset($price_max)) {
                $queryBuilder= $queryBuilder->setParameter('price_max', $price_max)
                                            -> andWhere('p.price <= :price_max');
            }        
            if (isset($price_min)) {
                $queryBuilder= $queryBuilder->setParameter('price_min', $price_min)
                                            -> andWhere('p.price >=:price_min');
            }  
            if (!empty($all_id)) {
                $queryBuilder= $queryBuilder-> setParameter('all_id', $all_id)
                                            -> andWhere('c.id in (:all_id)');
            }              
                
            $query=$queryBuilder->getQuery();
            $sql=$query->getSQL();
            $dql=$queryBuilder->getDQL();
            $params=$query->getParameters();

            $products = $queryBuilder->getQuery()->getResult();

            /* my first queries to database - sort by name/price/category:
                    
                    $products = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->findBy(['price' => $price,
                            'name' => $name,
                            ]);
             
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
                    'form' => $form->createView(),
                    'products' => $products,
                    'sql' => $sql,
                    'dql' => $dql,
                    'params' => $params
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
                    'form' => $form->createView(),
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