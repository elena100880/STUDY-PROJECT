<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductToCartController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
       
    
    public function product_tocart ($id)
    {
        $id_incart=$id;
               
        if ($this->session->get($id_incart) != null ) {
            
            $this->session->set($id_incart, $this->session->get($id_incart)+1);
        
        }
        else {
            $this->session->set($id_incart, 1);
        }
             
        return $this->redirectToRoute('product_edit', ['id' => $id]);
    }

    public function product_fromcart ($id)
    {
        $id_incart=$id;

        if ($this->session->get($id_incart) != 0 ) {
            
            $this->session->set($id_incart, $this->session->get($id_incart)-1);
        }       
       
        return $this->redirectToRoute('product_edit', ['id' => $id]);
    }




    
}