<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
    
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TaskType;
use App\Entity\Task;

class TaskController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/task', name: 'app_task')]
    public function new(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $taskdata = $form->getData();
            
            $task->setTask($task->getTask());
            $task->setDueDate($task->getDueDate());
            
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Task created.');
            
            return $this->redirectToRoute('app_task');
        }
        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
