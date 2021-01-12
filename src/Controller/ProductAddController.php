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

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductAddController extends AbstractController
{
    // slugger for version 2:
    private $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function productadd (Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
               
        $form = $this->createForm (ProductType::class, $product)
            ->add('image', FileType::class, ['label' => 'Add an image of the Product (JPG or PNG file):' ,
                                            'required' => false, 
                                            'attr' => array('accept'=> 'image/png, image/jpeg')  ] )
            ->add('save', SubmitType::class, ['label'=>'Add the item']);
         
        $form->handleRequest($request);
       

        if ($form->isSubmitted()) {
            
        // $image сохраняет загруженный PDF файл
        //   /** @var Symfony\Component\HttpFoundation\File\UploadedFile $image */

            //for version 1: $image = $product->getImage();
            $image = $form->get('image')->getData();
            
        // this condition is needed because the 'image' field is not required, so the  file must be processed only when a file is uploaded
            if ($image) {
        
        // this is needed to safely include the file name as part of the URL:
                
                //for version 1: $newImageName = $this->generateUniqueImageName().'.'.$image->guessExtension();
                $originalImageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalImageName);
                $newImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();
                
        // Move the file to the directory where images are stored
                $image->move(
                    $this->getParameter('images_directory'),
                    $newImageName
                );

        // обновляет свойство 'image', чтобы сохранить имя файла PDF вместо его содержаиия
                $product->setImage($newImageName);
            }
            
            $productManager = $this->getDoctrine()->getManager();
            $productManager->persist($product);
            $productManager->flush();
            
            return $this->redirectToRoute('products');
        }
        else 
        {
            $contents = $this->renderView('product_add/product_add.html.twig',
                    [
                        'form' => $form->createView(),
                    ],
                );
            return new Response($contents);
        }
        
    }

    // function for version 1:
    private function generateUniqueImageName()
    {
        // md5() уменьшает схожесть имён файлов, сгенерированных
        // uniqid(), которые основанный на временных отметках
        return md5(uniqid());
    }

}