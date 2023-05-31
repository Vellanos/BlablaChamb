<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ride;

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

    #[Route('/login', name: 'app_login')]
    public function connexion(): Response
    {
        return $this->render('home/login.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
