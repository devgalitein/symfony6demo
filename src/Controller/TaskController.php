<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
    
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TaskType;
use App\Entity\Task;
use App\Entity\TaskCategory;

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
        
        // $task_category = $this->entityManager->getRepository(TaskCategory::class)->find(3);
        // dd($task_category->getTasks()->toArray());
        
        // $taskData = $this->entityManager->getRepository(Task::class)->find(15);
        // dd($taskData->getCategory()->getName());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // $task_category = $this->entityManager->getRepository(TaskCategory::class)->find(3);
            $taskdata = $form->getData();
            
            $task->setTask($task->getTask());
            $task->setDueDate($task->getDueDate());
            // $task->setCategory($task_category);
            
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
