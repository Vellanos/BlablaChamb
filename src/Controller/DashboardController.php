<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\Rule;
use App\Entity\Ride;
use App\Form\AddCarType;
use App\Form\AddRuleType;
use App\Form\AddRideType;
use App\Form\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    #[Route('/', name: 'app_dashboard')]
    public function profil(): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/carForm', name: 'app_add_car')]
    public function addCar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $car = new Car();
        $formCar = $this->createForm(AddCarType::class, $car);
        $formCar->handleRequest($request);

        // Condition valide lorsque le formulaire est soumis et valide
        if ($formCar->isSubmitted() && $formCar->isValid()) {

            $car->setOwner($this->getUser());
            $car->setCreated(new \DateTime());

            $entityManager->persist($car);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/Caraddform.html.twig', [
            'controller_name' => 'DashboardController',
            'form' => $formCar
        ]);
    }

    #[Route('/ruleForm', name: 'app_add_rule')]
    public function addRule(Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $rule = new Rule();
        $formRule = $this->createForm(AddRuleType::class, $rule);
        $formRule->handleRequest($request);

        // Condition valide lorsque le formulaire est soumis et valide
        if ($formRule->isSubmitted() && $formRule->isValid()) {

            $rule->setAuthor($this->getUser());

            $entityManager->persist($rule);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('dashboard/Ruleaddform.html.twig', [
            'controller_name' => 'DashboardController',
            'form' => $formRule
        ]);
    }

    #[Route('/rideForm', name: 'app_add_ride')]
    public function addRide(Request $request, EntityManagerInterface $entityManager): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ride = new Ride();
        $formRide = $this->createForm(AddRideType::class, $ride);
        $formRide->handleRequest($request);

        // Condition valide lorsque le formulaire est soumis et valide
        if ($formRide->isSubmitted() && $formRide->isValid()) {

            $ride->setDriver($this->getUser());
            $ride->setCreated(new \DateTime());

            $entityManager->persist($ride);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('dashboard/Rideaddform.html.twig', [
            'controller_name' => 'DashboardController',
            'form' => $formRide,
        ]);
    }

    #[Route('/edituser', name: 'app_edit_user')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->security->getUser();

        $editUserForm = $this->createForm(EditUserType::class, $user);
        $editUserForm->handleRequest($request);
        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {

            // if (!empty($editUserForm['plainPassword']->getData())) {
            //     $password = $userPasswordHasher->hashPassword($user, $editUserForm['plainPassword']->getData());
            //     $user->setPassword($password);
            // }

            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        
        
        return $this->render('dashboard/EditUserForm.html.twig', [
            'controller_name' => 'DashboardController',
            'editUserForm' => $editUserForm,
        ]);
    }
}
