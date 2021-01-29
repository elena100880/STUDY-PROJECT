<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        //$id=$product->getId();
        $name1=$product->getName();
        $price1=$product->getPrice();
        $description1=$product->getDescription();
        $image1=$product->getImage();
        $category1=$product->getCategory();
               
        if ($image1 != null) {
            $product->setImage(
                new File($this->getParameter('images_directory').'/'.$product->getImage())
            );
        }

        $form1 = $this->createForm (ProductType::class, $product)
            ->add('image', FileType::class, ['label' => 'Image of the Product (JPG or PNG file):' ,
                                            'required' => false, 
                                            'attr' => array('accept'=> 'image/png, image/jpeg'),
                                                ])

            ->add('delimage', CheckboxType::class, ['label'=> 'Delete all images:',
                                                    'required' => false, 
                                                    'mapped' => false,
                                                    ])

            ->add('save', SubmitType::class, ['label'=> 'Save changes']);
       
        /*
            $this->createFormBuilder()
            ->add('name', TextType::class, ['label'=>'Name:', 'data' => $name1])
            ->add('price', NumberType::class, ['label'=>'Price in $:', 'data' => $price1])
            ->add('description', TextareaType::class, ['label'=>'Description of the item:', 'data' => $description1])
            ->add ('category', EntityType::class, [
                'class'=> Category::class,
                'choice_label' => 'name',
                'label' => 'Choose category:',
                'data' => $category1 
                ])
        
            ->add('image', FileType::class, ['label' => 'Image of the Product (JPG or PNG file):' ,
                                            'required' => false, 
                                            'attr' => array('accept'=> 'image/png, image/jpeg'),
                                               ]  )

            ->add('delimage', CheckboxType::class, ['label'=> 'Delete all images:',
                                                        'required' => false,   ])

            ->add('save', SubmitType::class, ['label'=> 'Save changes'])
            ->getForm();
        */
           
        $form2 = $this->createFormBuilder()
            ->add('send', SubmitType::class, ['label'=>'Delete the item!!'])
            ->getForm();
        
        $form1->handleRequest($request);
        $form2->handleRequest($request);

        //$quantity=$this->session->get($id);
        //$this->session->set('quantity', $quantity);

        if ($form1->isSubmitted()) {
            $save='saved';
            
            $image = $product->getImage();
            $checkbox = $form1->get('delimage')->getData();

            if ($checkbox) {

                $product->setImage(null);

            }   
            else {    
                if ($image) {
                                    
                    $imageName = $this->generateUniqueImageName().'.'.$image->guessExtension();
                    
                    $image->move(
                        $this->getParameter('images_directory'),
                        $imageName
                        );

                    $product->setImage($imageName);
                }
                else {
                    $product->setImage($image1);
                }
            }
            $productManager->flush();
                                      
            $contents = $this->renderView('product_edit/product_edit.html.twig',
                [
                    'form1' => $form1->createView(),
                    'form2' => $form2->createView(),
                    'id'=> $id,
                    'name1'=> $name1,
                    'price1'=> $price1,
                    'description1'=> $description1,
                    'category1' => $category1,
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
            $contents = $this->renderView('product_edit/product_edit.html.twig',
                    [
                        'form1' => $form1->createView(),
                        'form2' => $form2->createView(),
                        'id'=> $id,
                        'name1'=> $name1,
                        'price1'=> $price1,
                        'description1'=> $description1,
                        'category1' => $category1,
                        'image1' => $image1,
                        'product' => $product,
                        'quantity'=>$this->session->get($id, 0),
                    
                    ],
                );
            return new Response($contents);
        }
        
    }

    private function generateUniqueImageName()
    {
        // md5() уменьшает схожесть имён файлов, сгенерированных
        // uniqid(), которые основанный на временных отметках
        return md5(uniqid());
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