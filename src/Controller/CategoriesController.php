<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;


class CategoriesController extends AbstractController
{
    public function categories (Request $request)
    {
            $categories = $this->getDoctrine()
                ->getRepository(Category::class)
                ->findAll();
           
            $contents = $this->renderView('categories/categories.html.twig',
                [
                    'categories' => $categories,
                ],
            );    
        return new Response($contents);
    }
}