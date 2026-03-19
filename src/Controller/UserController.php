<?php

namespace App\Controller;

use App\Entity\User;
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
    public function account(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/new', name: '_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
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
