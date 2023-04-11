<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\WorkTag;
use App\Entity\Work;
use App\Form\WorkType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WorkController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/work', name: 'app_work')]
    public function index(): Response
    {
        return $this->render('work/index.html.twig', [
            'controller_name' => 'WorkController',
        ]);
    }
    
    #[Route('/work/new', name: 'app_work_new')]
    public function new(Request $request): Response
    {
        $work = new Work();

        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($work);
            $this->entityManager->flush();
        }

        return $this->render('work/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/work/edit/{id}', name: 'app_work_edit')]
    public function edit(Work $work, Request $request, EntityManagerInterface $entityManager): Response
    {
        $originalTags = new ArrayCollection();
        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($work->getWorkTags() as $tag) {
            $originalTags->add($tag);
        }
        
        $editForm = $this->createForm(WorkType::class, $work);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
           
            // remove the relationship between the tag and the Task
            foreach ($originalTags as $tag) {
                if (false === $work->getWorkTags()->contains($tag)) {
                    // if you wanted to delete the Tag entirely, you can also do that
                    $entityManager->remove($tag);
                }
            }
            
            $entityManager->persist($work);
            $entityManager->flush();
            return $this->redirectToRoute('app_work_edit', ['id' => $work->getId()]);
        }

        return $this->render('work/edit.html.twig', [
            'form' =>$editForm->createView(),
        ]);
    }
}
