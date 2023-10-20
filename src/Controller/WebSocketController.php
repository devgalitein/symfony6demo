<?php

namespace App\Controller;

use App\Entity\ChatMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WebSocketController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/websocket", name="websocket")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $chat_messages = $this->entityManager->getRepository(ChatMessage::class)->findAll();
        return $this->render('websocket/index.html.twig', [
            'chat_messages' => $chat_messages,
        ]);
    }
}
