<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
// use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\LoginHistory;

#[Route('/api', name: 'app_api_')]
class AuthController extends ApiController
{   
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    #[Route('/register', name: 'register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $request = $this->transformJsonBody($request);
        
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');

        if (empty($username) || empty($password) || empty($email)) {
            return $this->respondValidationError("Invalid Username or Password or Email");
        }
        
        $username_is_exists =  $this->em->getRepository(User::class)->findOneBy(['username'=>$username]);
        if(!empty($username_is_exists)) {
            return $this->respondValidationError("Username is already exists.");
        }
        
        $email_is_exists =  $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if(!empty($email_is_exists)) {
            return $this->respondValidationError("Email is already exists.");
        }

        $user = new User($username);
        
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($username);
        
        $this->em->persist($user);
        $this->em-> flush();
        
        return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }
    
    #[Route('/login_check',name:"login-check")]
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $login_history = new LoginHistory();
        $user->setToken(111);
        $user->setEmail(111);
        $user->setStatus(0);
        
        $this->em->persist($user);
        $this->em-> flush();
        
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
