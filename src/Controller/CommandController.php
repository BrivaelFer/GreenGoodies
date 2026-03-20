<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Command;
use App\Entity\Product;
use App\Service\CommandService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandController extends AbstractController
{
    private CommandService $commandService;

    public function __construct() {
        $this->commandService = new CommandService();
    }

    #[Route('/command', name: 'app_command')]
    public function showActiveCommand(): Response
    {
        /** @var User */
        $user = $this->getUser();
        
        $command = $this->commandService->getUserActiveCommand($user);

        $totals = $this->commandService->getTolals($command);

        return $this->render('command/index.html.twig', [
            'user' => $user,
            'command' => $command,
            'totals' => $totals
        ]);
    }
    
    #[Route('/command/add/{id}', name: 'app_command_add')]
    public function addPructToCommand(Product $product, EntityManagerInterface $entityManager): Response
    {
        /** @var User */
        $user = $this->getUser();
        
        $this->commandService->addProductToCommand($product, $user, $entityManager);

        return $this->redirectToRoute('app_command');
    }

    #[Route('/command/valide/{id}', name: 'app_command_valide')]
    public function valideCommand(Command $command, EntityManagerInterface $entityManager): Response
    {
        /** @var User */
        $user = $this->getUser();
        if($user->getId() === $command->getUser()->getId()) {
            $this->commandService->validateCommand($command, $entityManager);
        }
        return $this->redirectToRoute('app_command');
    }

    #[Route('/command/delete/{id}', name: 'app_command_delete')]
    public function deleteCommand(Command $command, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($command);
        $entityManager->flush();

        return $this->redirectToRoute('app_command');
    }
}
