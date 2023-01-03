<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'app_api_')]
class APIDashboardController extends AbstractController
{

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        
        if(!$this->isGranted('ROLE_ADMIN')) {
            return $this->json([
                'message' => 'Access Denied!',
            ]);
        }

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DashboardController.php',
        ]);
    }


    #[Route('/logout', name: 'logout')]
    public function logout(): Response {
        $user = $this->getUser();
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }
}
