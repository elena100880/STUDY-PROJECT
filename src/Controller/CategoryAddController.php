<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

class CategoryAddController extends AbstractController
{
    public function categoryadd (Request $request, $id)
    {
        $categoryManager = $this->getDoctrine()->getManager();
        $category = new Category();

        //  version with representation of the category with the given id as a parent category:
        
            if ($id>0) {
                $categoryParent = $categoryManager->getRepository(Category::class)->find($id);
                $category->setName('');
                $category->setParent($categoryParent);
            }
        

        //  version with representation of the category with the given id as a child category:
        /*
           if ($id>0) {
                $category = $categoryManager->getRepository(Category::class)->find($id);
            }
        */
        
        $form = $this->createForm (CategoryType::class, $category)
            ->add('save', SubmitType::class, ['label'=>'Add the category']);
         
        $form->handleRequest($request);
               
        if ($form->isSubmitted()) {
           
            $categoryManager->persist($category);
            $categoryManager->flush();

            return $this->redirectToRoute('product_add');
        }
        else 
        {
            $contents = $this->renderView('category_add/category_add.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                );
            return new Response($contents);
        }
    }
}