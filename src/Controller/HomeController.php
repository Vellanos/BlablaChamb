<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
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
