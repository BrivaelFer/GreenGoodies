<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/user', 'app_user')]
final class UserController extends AbstractController
{
    #[Route('/', name: '_account')]
    #[IsGranted('ROLE_USER')]
    public function account(): Response
    {
        /** @var User */
        $user = $this->getUser();
        return $this->render('user/account.html.twig', [
            'user' => $user,
            'commandes' => $user->getCommands()
        ]);
    }

    #[Route('/new', name: '_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
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
}
