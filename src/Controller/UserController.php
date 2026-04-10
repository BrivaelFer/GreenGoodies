<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\CalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', 'app_user')]
final class UserController extends AbstractController
{
    #[Route('/', name: '_account')]
    #[IsGranted('ROLE_USER')]
    public function account(#[CurrentUser]User $user, CalculationService $calculationService): Response
    {
        $commands = $user->getCommands();

        $totals = [];
        foreach($commands as $command){
            $totals[$command->getId()] = $calculationService->calculateCommandTotal($command);
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'commands' => $commands,
            'totals' => $totals,
        ]);
    }

    #[Route('/new', name: '_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(User $userToDelete, EntityManagerInterface $entityManager): Response
    {
        $deleted = false;
        if($this->isGranted('ROLE_ADMIN') || $this->getUser()->getUserIdentifier() === $userToDelete->getId()){
            $entityManager->remove($userToDelete);
            $entityManager->flush();
            $deleted = true;
        }

        return $this->redirectToRoute('app_home', ['deleted' => $deleted]);
    }

    #[Route('/activer-api', name:'_activat_api')]
    #[IsGranted('ROLE_USER')]
    public function activatApi(#[CurrentUser]User $user): Response
    {
        return $this->redirectToRoute('app_user_account');
    }
}
