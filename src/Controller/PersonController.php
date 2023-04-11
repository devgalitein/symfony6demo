<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\PersonFormType;
use App\Entity\Person;
use App\Entity\City;
use App\Entity\Neighborhood;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

class PersonController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/person/get-neighborhoods-from-city', name: 'person_list_neighborhoods')]
    public function listNeighborhoodsOfCityAction(Request $request)
    {
        // Get Entity manager and repository
        $neighborhoodsRepository = $this->em->getRepository(Neighborhood::class);
        // dd($neighborhoodsRepository);
        // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
        $neighborhoods = $neighborhoodsRepository->createQueryBuilder("q")
            ->where("q.city = :cityid")
            ->setParameter("cityid", $request->query->get("cityid"))
            ->getQuery()
            ->getResult();
        // Serialize into an array the data that we need, in this case only name and id
        // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
        $responseArray = array();
        foreach($neighborhoods as $neighborhood){
            $responseArray[] = array(
                "id" => $neighborhood->getId(),
                "name" => $neighborhood->getName()
            );
        }
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);
    }
    
    #[Route('/person', name: 'app_person')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->em->getRepository(Person::class)->getWithSearchQueryBuilder();

        $persons = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('person/index.html.twig', [
            'controller_name' => 'PersonController',
            'persons' => $persons,
        ]);
    }
    
    #[Route('/person/new', name: 'app_person_new')]
    #[Route('/person/{id}', name: 'app_person_edit')]
    public function add_edit(Request $request,$id =null): Response
    {
        if($id != null) {
            $person = $this->em->getRepository(Person::class)->find($id);
            if(empty($person)) {
                return $this->redirectToRoute('app_person');
            }
        } else {
            $person = new Person();
        }

        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($person);
            $this->em->flush();
            if($id != null) {
                return $this->redirectToRoute('app_person_edit',['id'=>$id]);
            } else {
                return $this->redirectToRoute('app_person');
            }
        }

        return $this->render('person/add_edit.html.twig', [
            'controller_name' => 'PersonController',
            'form' => $form->createView(),
            'person' => $person,
        ]);
    }

}
