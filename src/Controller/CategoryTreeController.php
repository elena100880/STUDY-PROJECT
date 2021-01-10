<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

class CategoryTreeController extends AbstractController
{
    public function categorytree (Request $request, $id)
    {
        $categoryManager = $this->getDoctrine()->getManager();
        $category = $categoryManager->getRepository(Category::class)->find($id);
        
        function getTree ($child, $i) {
            
            $names= Array($i.$child->getName());  
            $childs=$child->getChildCategories(); 
            
           // if ( !empty($childs) ){
                //$i=$i."++";       
            foreach ($childs as $child) {
                $names=array_merge ($names, getTree ($child, $i."++"));
            }
            //}  
            return $names;
        } 

        $names=getTree ($category, "");
        
        $contents = $this->renderView('category_tree/category_tree.html.twig',
            [   'names' => $names,
                ]);
        return new Response($contents);
    }
}