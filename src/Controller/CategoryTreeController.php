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
        
        //my very first version:
        /*    
                function getTree ($child) {
                    
                    $names= Array($child->getName());  
                    
                    $childs=$child->getChildCategories(); 

                    if ( !empty($childs) ){
                        
                        foreach ($childs as $child) {
                            $names=array_merge ($names, getTree ($child));

                        }
                    }  
                    return $names;
                }  
         */  
        
        //final version with recursion:
        function getTree ($child, $i) {
            
            $names= Array($i.$child->getName() );  
            $children=$child->getChildCategories();
            
            foreach ($children as $child) {
                $names=array_merge ($names, getTree ($child, $i."++" ) );
            }
            return $names;
        } 

    $names=getTree ($category, "");
        
        $contents = $this->renderView('category_tree/category_tree.html.twig',
            [   'names' => $names,
                'id' => $id,
                ]);
        return new Response($contents);
    }
}