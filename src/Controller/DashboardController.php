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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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

            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }


        return $this->render('dashboard/EditUserForm.html.twig', [
            'controller_name' => 'DashboardController',
            'editUserForm' => $editUserForm,
        ]);
    }

    #[Route('/editcar/{id}', name: 'app_edit_car')]
    public function editCar(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $car = $entityManager->getRepository(Car::class)->find($id);

        if (!$car) {
            throw $this->createNotFoundException('Car not found');
        }

        $editCarForm = $this->createForm(AddCarType::class, $car);
        $editCarForm->handleRequest($request);

        if ($editCarForm->isSubmitted() && $editCarForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/EditCarForm.html.twig', [
            'controller_name' => 'DashboardController',
            'editCarForm' => $editCarForm,
            'car' => $car, // Ajout de la variable car
        ]);
    }

    #[Route('/editrule/{id}', name: 'app_edit_rule')]
    public function editRule(Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $rule = $entityManager->getRepository(Rule::class)->find($id);

        if (!$rule) {
            throw $this->createNotFoundException('Rule not found');
        }

        $editRuleForm = $this->createForm(AddRuleType::class, $rule);
        $editRuleForm->handleRequest($request);

        if ($editRuleForm->isSubmitted() && $editRuleForm->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/EditRuleForm.html.twig', [
            'controller_name' => 'DashboardController',
            'editRuleForm' => $editRuleForm,
            'rules' => $rule, // Ajout de la variable rule
        ]);
    }

    #[Route('/editride/{id}/{from}', name: 'app_edit_ride', defaults: ['from' => 'dashboard'])]
    public function editRide(Request $request, EntityManagerInterface $entityManager, $id, $from): Response

    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $ride = $entityManager->getRepository(Ride::class)->find($id);

        if (!$ride) {
            throw $this->createNotFoundException('Ride not found');
        }

        $editRideForm = $this->createForm(AddRideType::class, $ride, [
            'action' => $this->generateUrl('app_edit_ride', ['from' => $from, 'id' => $ride->getId()])
        ]);
        $editRideForm->handleRequest($request);

        if ($editRideForm->isSubmitted() && $editRideForm->isValid()) {
            $entityManager->flush();
            if ($from === 'dashboard') {
                return $this->redirectToRoute('app_dashboard');
            } elseif ($from === 'details') {
                return $this->redirectToRoute('app_trajets_details', ['Id' => $ride->getId()]);
            }
        }

        return $this->render('dashboard/EditRideForm.html.twig', [
            'controller_name' => 'DashboardController',
            'editRideForm' => $editRideForm,
            'rides' => $ride, // Ajout de la variable rule
        ]);
    }

    #[Route('/car/delete/{id}', name: 'app_car_delete')]
    public function carDelete(EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer le répertoire de l'entité Product
        $repository = $entityManager->getRepository(Car::class);

        // Cherche un produit grâce à sa primary key
        // La variable $id est issue du paramètre de l'url, voir l'attribut Route de la fonction
        $car = $repository->find($id);

        // On vérifie que l'on a bien récupéré un produit en base de données,
        // si ce n'est pas le cas il n'y a pas de produit à modifier et l'on retourne une erreur à l'utilisateur
        if (!$car) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        // Supprime le produit et persiste les changements
        $entityManager->remove($car);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/rule/delete/{id}', name: 'app_rule_delete')]
    public function ruleDelete(EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer le répertoire de l'entité Product
        $repository = $entityManager->getRepository(Rule::class);

        // Cherche un produit grâce à sa primary key
        // La variable $id est issue du paramètre de l'url, voir l'attribut Route de la fonction
        $rule = $repository->find($id);

        // On vérifie que l'on a bien récupéré un produit en base de données,
        // si ce n'est pas le cas il n'y a pas de produit à modifier et l'on retourne une erreur à l'utilisateur
        if (!$rule) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        // Supprime le produit et persiste les changements
        $entityManager->remove($rule);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/ride/delete/{id}/{from}', name: 'app_ride_delete', defaults: ['from' => 'dashboard'])]
    public function rideDelete(EntityManagerInterface $entityManager, int $id, string $from): Response
    {
        // Récupérer le répertoire de l'entité Product
        $repository = $entityManager->getRepository(Ride::class);

        // Cherche un produit grâce à sa primary key
        // La variable $id est issue du paramètre de l'url, voir l'attribut Route de la fonction
        $ride = $repository->find($id);

        // On vérifie que l'on a bien récupéré un produit en base de données,
        // si ce n'est pas le cas il n'y a pas de produit à modifier et l'on retourne une erreur à l'utilisateur
        if (!$ride) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        
        // Supprime le produit et persiste les changements
        $entityManager->remove($ride);
        $entityManager->flush();

        
        if ($from == "details") {
            return $this->redirectToRoute('app_trajets');
        } else {
            return $this->redirectToRoute('app_dashboard');
        }
        
    }
}
