<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FindRideType;
use App\Entity\Ride;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(Request $request): Response
    {
        $form = $this->createForm(FindRideType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $request->getSession()->set('searchData', $data);

            return $this->redirectToRoute('app_show_rides');
        }
        
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/foundRides', name: 'app_show_rides')]
    public function showRides(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = $request->getSession()->get('searchData', []);

        $rides = [];
        if (!empty($data)) {
            $ridesrepository = $entityManager->getRepository(Ride::class);
            $rides = $ridesrepository->findByParameters(
                $data['depart'],
                $data['destination'],
                $data['date'],
                $data['seats']
            );
        }

        return $this->render('home/showRides.html.twig', [
            'controller_name' => 'HomeController',
            'rides' => $rides,
        ]);
    }

    #[Route('/trajets', name: 'app_trajets')]
    public function trajets(EntityManagerInterface $entityManager): Response
    {
        // Récupérer le répertoire de l'entité Ride
        $repository = $entityManager->getRepository(Ride::class);
        $rides = $repository->findAll();
        return $this->render('home/trajets.html.twig', [
            'controller_name' => 'HomeController',
            'rides' => $rides,
        ]);
    }

    #[Route('/trajets/details', name: 'app_trajets_details')]
    public function details(EntityManagerInterface $entityManager,Request $request): Response
    {
        $rideId = ($request->query->get("Id"));
        $repository_ride = $entityManager->getRepository(Ride::class);
        $rides = $repository_ride->findOneBy(['id' => $rideId]);

        return $this->render('home/details.html.twig', [
            'controller_name' => 'HomeController',
            'rides' => $rides
        ]);
    }

    #[Route('/trajets/reservation', name: 'app_resa')]
    public function reservation(EntityManagerInterface $entityManager,Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('home/reservation.html.twig', [
            'controller_name' => 'HomeController',
            
        ]);
    }

}
