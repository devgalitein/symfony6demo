<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ProductValidator;
use App\Service\EmailService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SmsNotification;
use Symfony\Component\Messenger\Stamp\DelayStamp;

use Symfony\Component\EventDispatcher\EventDispatcher;
use App\EventListener\ProductAddListener;
use Symfony\Contracts\EventDispatcher\Event;
use App\Events\ProductEvent;
use App\Entity\Size;
use App\Entity\Colors;
use App\Entity\ProductVariation;
use App\Form\ProductFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductValidator $productValidator;
    private EmailService $emailService;
    public function __construct(EntityManagerInterface $entityManager,ProductValidator $productValidator,EmailService $emailService)
    {
        $this->entityManager = $entityManager;
        $this->productValidator = $productValidator;
        $this->emailService = $emailService;
    }

    
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $product->setName('name');
            $product->setDescription('description');
            $product->setPrice(0);
            
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_product_new');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/product/edit/{id}', name: 'app_product_edit')]
    public function edit(Product $product, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $product->setName('name1');
            $product->setDescription('description1');
            $product->setPrice(0);
            
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_product_edit', ['id' => $product->getId()]);
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    
    #[Route('/get_products', name: 'get_products_list_datatables')]
    public function getProducts(Request $request){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $draw = intval($request->request->get('draw'));
        $start = $request->request->get('start');
        $length = $request->request->get('length');
        $search = $request->request->all('search');
        $orders = $request->request->all('order');
        $columns = $request->request->all('columns');
        
        foreach ($orders as $key => $order)
        {
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }

        $records = $this->entityManager->getRepository(Product::class)->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
        
        // Get total number of objects
        $totalRecords =  $this->entityManager->getRepository(Product::class)->countResults();

        // Get total number of filtered data
        $totalRecordswithFilter = $records["countResult"];
        
        $data_arr = array();
        
        foreach($records['results'] as $record){
            
            $id = $record->getId();
            $name = $record->getName();
            $description = $record->getDescription();
            $price = $record->getPrice();
            $product_image = $record->getProductImage();
            $created_at = $record->getCreatedAt()->format('Y-m-d H:i:s');

            $product_image = '/uploads/product_images/'. $product_image;
            $product_image ='<img src="'.$product_image.'" height="50px" width="50px"/>';

            $tmp_data_arr = array(
                "id" => $id,
                "name" => $name,
                "description" => $description,
                "price" => $price,
                "product_image" =>  $product_image,
                "created_at" => $created_at,
            );
            
            if($this->isGranted('ROLE_ADMIN')) {
                $button = '';
                $button .= '<button type="button" class="action_edit btn btn-warning me-1" data-id="'.$id.'" >Edit</button>';
                $button .= '<button type="button" class="action_delete btn btn-danger" data-id="'.$id.'" >Delete</button>';
                $tmp_data_arr['action'] = $button;
            }
            
            $data_arr[] = $tmp_data_arr;
        }

        $response = array(
            "draw" => $draw,
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        return new JsonResponse($response);
    }
    
    #[Route('/add_edit_product', name: 'add_edit_product')]
    public function addeditAction(Request $request,MessageBusInterface $bus){
        
        $this->denyAccessUnlessGranted('ROLE_CREATOR');
        
        $id = $request->request->get('edit_id') ?? null;
        
        $product_image = $request->files->get('product_image');

        $requestParams['edit_id'] = $id;
        $requestParams['name'] = $request->request->get('name');
        $requestParams['description'] = $request->request->get('description');
        $requestParams['price'] = $request->request->get('price');
        $requestParams['product_image'] = $product_image;
        
        if($id != null) {
            $product = $this->entityManager->getRepository(Product::class)->find($id);
            if(isset($product) && empty($product)) {
                $return = ['success'=> 2,'msg'=> 'Product Not Found'];
                return new JsonResponse($return);
            }
        } else {
            $product = new Product();
        }
        
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        $errorMessages = $this->productValidator->validateData($requestParams);
        if ($errorMessages) {
            $return = ['errors'=> $errorMessages];
            return new JsonResponse($return);
        }
        
        $product->setName($requestParams['name']);
        $product->setDescription($requestParams['description']);
        $product->setPrice($requestParams['price']);

        if ($product_image) {

            if($id != null) {
                $product_image_dir = $this->getParameter('product_image_dir');
                @unlink($product_image_dir.'/'.$product->getProductImage());
            }
            
            $originalFilename = pathinfo($product_image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            // $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$product_image->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $product_image->move(
                    $this->getParameter('product_image_dir'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $product->setProductImage($newFilename);
        }
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        if($id != null) {
            $return = ['success'=>1,'msg'=>'Product Updated'];
        } else {
            
            $bus->dispatch(new SmsNotification('New Product created!'),[
                // wait 5 seconds before processing
                new DelayStamp(5000),
            ]);

            $listener = new ProductAddListener();
            $dispatcher = new EventDispatcher();
            $dispatcher->addListener('product.event', [$listener, 'onProductEvent']);
            $dispatcher->dispatch(new ProductEvent($product), ProductEvent::NAME);           
            
            $to_email = '';
            $subject = 'New Product Created: '.$product->getName();
            $template_path = 'emails/product_added.html.twig';
            
            $parameter_array['name'] = $product->getName();
            $parameter_array['description'] = $product->getDescription();
            $parameter_array['price'] = $product->getPrice();
            
            try {
                $this->emailService->sendTemplatedEmail($to_email,$subject,$template_path,$parameter_array);
            } catch (Exception $exception) {
    
            }
            
            $return = ['success'=>1,'msg'=>'Product Created'];
        }
        
        return new JsonResponse($return);
    }

    
    #[Route('/delete_product/{id}', name: 'delete_product')]
    public function deleteAction(Request $request, Product $product){
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $return = [];
        $this->entityManager->getRepository(Product::class)->remove($product);
        $this->entityManager->flush();
        $return = ['success'=>1,'msg'=>'deleted'];
        
        return new JsonResponse($return);
    }
    
    #[Route('/edit_product/{id}', name: 'edit_product')]
    public function editAction(Request $request, Product $product){
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $product_variation = [];
        
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);
        
        $product_variations = $this->render('product/product_variations.html.twig', [
            'form' => $form->createView(),
        ])->getContent();

        $data['id'] = $product->getId();
        $data['name'] = $product->getName();
        $data['description'] = $product->getDescription();
        $data['price'] = $product->getPrice();
        $data['product_variations'] = $product_variations;
        
        $return = ['success'=>1,'msg'=>'Data Found','data' => $data];
        
        return new JsonResponse($return);
    }
}
