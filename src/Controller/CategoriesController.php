<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;



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