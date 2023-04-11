<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Country;
use App\Entity\State;
use App\Entity\City;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\UserValidator;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SmsNotification;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route(
    path: '/{_locale}',
    requirements: [
        '_locale' => 'en|fr|de',
    ],
)]
class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserValidator $userValidator;
    public function __construct(EntityManagerInterface $entityManager,UserValidator $userValidator)
    {
        $this->entityManager = $entityManager;
        $this->userValidator = $userValidator;
    }

    
    #[Route('/users', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $country_list = $this->entityManager->getRepository(Country::class)->findAll();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'country_list' => $country_list
        ]);
    }
    
    #[Route('/get_users', name: 'get_users_list_datatables')]
    public function getUsers(Request $request){

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

        $records = $this->entityManager->getRepository(User::class)->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
        
        // Get total number of objects
        $totalRecords =  $this->entityManager->getRepository(User::class)->countResults();

        // Get total number of filtered data
        $totalRecordswithFilter = $records["countResult"];
        
        $data_arr = array();
        
        foreach($records['results'] as $record){
            
            $id = $record->getId();
            $user_name = $record->getUsername();
            $email = $record->getEmail();
            $city = ($record->getCity()) ? $record->getCity()->getName() : '';
            $state = ($record->getState()) ? $record->getState()->getName() : '';
            $country = ($record->getCountry()) ? $record->getCountry()->getName() : '';

            $tmp_data_arr = array(
                "id" => $id,
                "username" => $user_name,
                "email" => $email,
                "city" => $city,
                "state" => $state,
                "country" => $country,
            );
            
            if($this->isGranted('ROLE_ADMIN')) {
                $button = '';
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
    
    #[Route('/add_edit_user', name: 'add_edit_user')]
    public function addeditAction(Request $request,UserPasswordHasherInterface $passwordHasher){
        // dd( $request->request->all());
        $this->denyAccessUnlessGranted('ROLE_CREATOR');
        
        $id = $request->request->get('edit_id') ?? null;

        $requestParams['edit_id'] = $id;
        $requestParams['username'] = $request->request->get('username');
        $requestParams['email'] = $request->request->get('email');
        $requestParams['password'] = $request->request->get('password');
        $requestParams['country'] = $request->request->get('country');
        $requestParams['state'] = $request->request->get('state');
        $requestParams['city'] = $request->request->get('city');
        
        if($id != null) {
            $user = $this->entityManager->getRepository(User::class)->find($id);
            if(isset($user) && empty($user)) {
                $return = ['success'=> 2,'msg'=> 'User Not Found'];
                return new JsonResponse($return);
            }
        } else {
            $user = new User();
        }
        
        $errorMessages = $this->userValidator->validateData($requestParams);
        if ($errorMessages) {
            $return = ['errors'=> $errorMessages];
            return new JsonResponse($return);
        }
        
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $requestParams['password']
        );
        
        $country  = $this->entityManager->getRepository(Country::class)->find($requestParams['country']);
        $state  = $this->entityManager->getRepository(State::class)->find($requestParams['state']);
        $city  = $this->entityManager->getRepository(City::class)->find($requestParams['city']);
        
        $user->setUsername($requestParams['username']);
        $user->setEmail($requestParams['email']);
        $user->setPassword($hashedPassword);
        $user->setCountry($country);
        $user->setState($state);
        $user->setCity($city);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        if($id != null) {
            $return = ['success'=>1,'msg'=>'User Updated'];
        } else {
            $return = ['success'=>1,'msg'=>'User Created'];
        }
        
        return new JsonResponse($return);
    }
    
    #[Route('/get_state_of_country', name: 'get_state_of_country')]
    public function listStateOfCountryAction(Request $request)
    {
        $state_list = $this->entityManager
            ->getRepository(State::class)
            ->createQueryBuilder("q")
            ->where("q.country = :country_id")
            ->setParameter("country_id", $request->query->get("country_id"))
            ->getQuery()
            ->getResult();
        
        $responseArray = array();
        foreach($state_list as $state){
            $responseArray[] = array(
                "id" => $state->getId(),
                "name" => $state->getName()
            );
        }
        
        $return = ['success'=>1,'msg'=>'State List','results'=>$responseArray];
        return new JsonResponse($return);
    }
    
    #[Route('/get_city_of_state', name: 'get_city_of_state')]
    public function listCityOfStateAction(Request $request)
    {
        $city_list = $this->entityManager
            ->getRepository(City::class)
            ->createQueryBuilder("q")
            ->where("q.state = :state_id")
            ->setParameter("state_id", $request->query->get("state_id"))
            ->getQuery()
            ->getResult();
        
        $responseArray = array();
        foreach($city_list as $city){
            $responseArray[] = array(
                "id" => $city->getId(),
                "name" => $city->getName()
            );
        }
        
        $return = ['success'=>1,'msg'=>'City List','results'=>$responseArray];
        return new JsonResponse($return);
    }
    
    #[Route('/delete_user/{id}', name: 'delete_user')]
    public function deleteAction(Request $request, User $user){
        
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $return = [];
        $this->entityManager->getRepository(User::class)->remove($user);
        $this->entityManager->flush();
        $return = ['success'=>1,'msg'=>'deleted'];
        
        return new JsonResponse($return);
    }
    
}
