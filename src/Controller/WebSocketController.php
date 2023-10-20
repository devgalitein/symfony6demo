<?php

namespace App\Controller;

use App\Entity\ChatMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebSocketController extends AbstractController
{
    /**
     * @Route("/websocket", name="websocket")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('websocket/index.html.twig', [
            'controller_name' => 'WebsocketController',
        ]);
    }
}
