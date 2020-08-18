<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\File\File;

class ProductEditController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function productedit (Request $request, $id)
    {
        $productManager = $this->getDoctrine()->getManager();
        $product = $productManager->getRepository(Product::class)->find($id);

        $id=$product->getId();
        $name1=$product->getName();
        $price1=$product->getPrice();
        $description1=$product->getDescription();
        $image1=$product->getImage();
               
        if ($image1 != null) {
            $product->setImage(
                new File($this->getParameter('images_directory').'/'.$product->getImage())
            );
        }
        
        $form1 = $this->createForm (ProductType::class, $product)
            ->add('image', FileType::class, ['label' => 'Image of the Product (JPG or PNG file):' ,
                                            'required' => false, 
                                            'attr' => array('accept'=> 'image/png, image/jpeg')  ] )

            ->add('save', SubmitType::class, ['label'=> 'Save changes']);
           
        $form2 = $this->createFormBuilder()
            ->add('send', SubmitType::class, ['label'=>'Delete the item!!'])
            ->getForm();
        
        $form1->handleRequest($request);
        $form2->handleRequest($request);

        //$quantity=$this->session->get($id);
        //$this->session->set('quantity', $quantity);


        if ($form1->isSubmitted()) {
            $save='saved';
            $productManager->flush();
                                      
            $contents = $this->renderView('productedit/index.html.twig',
                [
                    'form1' => $form1->createView(),
                    'form2' => $form2->createView(),
                    'id'=> $id,
                    'name1'=> $name1,
                    'price1'=> $price1,
                    'description1'=> $description1,
                    'image1' => $image1,
                    'product' => $product,
                    'save'=>$save,
                    'quantity'=>$this->session->get($id),
                ],
            );
            return new Response($contents);
        }
        else if ($form2->isSubmitted()) {
            
            return $this->redirectToRoute('product_delete', ['id' => $id]);
            
        }
        else 
        {
            $contents = $this->renderView('productedit/index.html.twig',
                    [
                        'form1' => $form1->createView(),
                        'form2' => $form2->createView(),
                        'id'=> $id,
                        'name1'=> $name1,
                        'price1'=> $price1,
                        'description1'=> $description1,
                        'image1' => $image1,
                        'product' => $product,
                        'quantity'=>$this->session->get($id, 0),
                    ],
                );
            return new Response($contents);
        }
        
    }

    public function productdelete ($id)
    {
        $productManager = $this->getDoctrine()->getManager();
        $product = $productManager->getRepository(Product::class)->find($id);
        
        $productManager->remove($product);
        $productManager->flush();

        return $this->redirectToRoute('products');
    }

    



}