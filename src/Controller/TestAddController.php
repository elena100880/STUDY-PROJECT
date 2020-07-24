<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestAddController extends AbstractController
{
    /**
     * @Route("/test/add", name="test_add")
     */
    public function index()
    {
        return $this->render('test_add/index.html.twig', [
            'controller_name' => 'TestAddController',
        ]);
    }
}
