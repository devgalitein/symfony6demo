<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Competition;
use App\Entity\Team;

class RelationshipController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/relationship', name: 'app_relationship')]
    public function index(): Response
    {
        $competition = $this->entityManager->getRepository(Competition::class)->findBy(array(),array('id'=>'ASC'),1,0);
        $competition = $competition[0];
        dump('competition data',$competition,$competition->getTeams()->toArray());
        
        $team = $this->entityManager->getRepository(Team::class)->findBy(array(),array('id'=>'ASC'),1,0);
        $team = $team[0];
        
        dd('team data',$team,$team->getCompetitions()->toArray());

        return $this->render('relationship/index.html.twig', [
            'controller_name' => 'RelationshipController',
        ]);
    }

    #[Route('/relationship/adddata', name: 'app_relationship_adddata')]
    public function adddata(): Response
    {
        $unique_id = uniqid();
        $competition = new Competition();
        $competition->setName('Champions League '.$unique_id);
        
        $this->entityManager->persist($competition);
        $this->entityManager->flush();

        $team = new Team();
        $team->setName('Fenerbahce'.$unique_id);
        
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        dd('Success');
    }

    #[Route('/relationship/linkdata', name: 'app_relationship_linkdata')]
    public function linkdata(): Response
    {
        /** @var Competition $competition */
        $unique_id = uniqid();
        
        $competition = $this->entityManager->getRepository(Competition::class)->findBy(array(),array('id'=>'ASC'),1,0);
        $competition = $competition[0];
        // dd($competition);
 
        $team = new Team();
        $team->setName('Arsenal '.$unique_id);
        $team->addCompetition($competition);
        // You can use both of them at same time but using one over another is more logical as cascade={"persist"} will handle it for us.
        // $competition->addTeam($team);
        $this->entityManager->persist($team);
        $this->entityManager->flush();
        

        /** @var Team $team */
        
        /*$team = $this->entityManager->getRepository(Team::class)->findBy(array(),array('id'=>'ASC'),1,0);
        $team = $team[0];
        
        $competition = new Competition();
        $competition->setName('Euro League 3 '.$unique_id);
        $competition->addTeam($team);
        
        // $team->addCompetition($competition);
        // This will not cause any problem so is optional as cascade={"persist"} will handle it for us.
        $this->entityManager->persist($competition);
        $this->entityManager->flush();
        */
        
        dd('Success');
    }
}
