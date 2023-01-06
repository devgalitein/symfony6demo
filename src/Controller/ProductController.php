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
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SmsNotification;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class ProductController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ProductValidator $productValidator;
    public function __construct(EntityManagerInterface $entityManager,ProductValidator $productValidator)
    {
        $this->entityManager = $entityManager;
        $this->productValidator = $productValidator;
    }

    
    #[Route('/product', name: 'app_product')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
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

            $tmp_data_arr = array(
                "id" => $id,
                "name" => $name,
                "description" => $description,
                "price" => $price,
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
        
        $requestParams['edit_id'] = $id;
        $requestParams['name'] = $request->request->get('name');
        $requestParams['description'] = $request->request->get('description');
        $requestParams['price'] = $request->request->get('price');
        
        if($id != null) {
            $product = $this->entityManager->getRepository(Product::class)->find($id);
            if(isset($product) && empty($product)) {
                $return = ['success'=> 2,'msg'=> 'Product Not Found'];
                return new JsonResponse($return);
            }
        } else {
            $product = new Product();
        }
        
        $errorMessages = $this->productValidator->validateData($requestParams);
        if ($errorMessages) {
            $return = ['errors'=> $errorMessages];
            return new JsonResponse($return);
        }
        
        $product->setName($requestParams['name']);
        $product->setDescription($requestParams['description']);
        $product->setPrice($requestParams['price']);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        if($id != null) {
            $return = ['success'=>1,'msg'=>'Product Updated'];
        } else {
            
            $bus->dispatch(new SmsNotification('New Product created!'),[
                // wait 5 seconds before processing
                new DelayStamp(5000),
            ]);

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
        
        $data['id'] = $product->getId();
        $data['name'] = $product->getName();
        $data['description'] = $product->getDescription();
        $data['price'] = $product->getPrice();
        
        $return = ['success'=>1,'msg'=>'Data Found','data' => $data];
        
        return new JsonResponse($return);
    }
}