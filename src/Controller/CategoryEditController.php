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

class CategoryEditController extends AbstractController
{
    public function categoryedit (Request $request, $id)
    {
        $categoryManager = $this->getDoctrine()->getManager();
        $category = $categoryManager->getRepository(Category::class)->find($id);

        $childCategories=$category->getChildCategories();
        
        $form1 = $this->createForm (CategoryType::class, $category)
            ->add('save', SubmitType::class, ['label'=> 'Save changes']);
               
        $form1->handleRequest($request);
        
        if ($form1->isSubmitted()) {
            
            $categoryManager->flush();
        }
       
        $contents = $this->renderView('category_edit/category_edit.html.twig',
             [
                'form1' => $form1->createView(),
                'id'=> $id,
                'category' => $category,
                'childCategories' => $childCategories
            ],
        );
        return new Response($contents);
    }
}