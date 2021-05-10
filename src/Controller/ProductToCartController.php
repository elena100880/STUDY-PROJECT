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
        
    public function product_tocart (Request $request, $id)
    {
        $id_incart=$id;
               
        if ($this->session->get($id_incart) != null ) {
            
            $this->session->set($id_incart, $this->session->get($id_incart)+1);
            

        }
        else {
            $this->session->set($id_incart, 1);
        }
        $totalQuantityOfItemsInCart = $this->session->get('totalQuantity');
        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart + 1);
        $this->session->remove('note'.$id);

        $referer = $request->headers->get('referer');   
        return $this->redirect($referer);
    }

    public function product_fromcart (Request $request, $id)
    {
        $id_incart=$id;

        if ($this->session->get($id_incart) > 1) {
            
            $this->session->set($id_incart, $this->session->get($id_incart)-1);
        }
        else {
            
            $this->session->remove($id_incart);
            
        }     
       
        $totalQuantityOfItemsInCart = $this->session->get('totalQuantity');
        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart - 1);
        $this->session->remove('note'.$id);

        $referer = $request->headers->get('referer');   
        return $this->redirect($referer);
    }

    public function deleteWholeProductFromCart ($id)
    {        
        $id_incart=$id;
        $removedQuantity = $this->session->get($id_incart);
        $this->session->remove($id_incart);

        $totalQuantityOfItemsInCart = $this->session->get('totalQuantity');
        $this->session->set('totalQuantity', $totalQuantityOfItemsInCart - $removedQuantity);
        
        return $this->redirectToRoute('cart_view', ['id' => $id]);
    }
    
}