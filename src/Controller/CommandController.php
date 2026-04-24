<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Command;
use App\Entity\Product;
use App\Service\CalculationService;
use App\Service\Command\CommandReaderService;
use App\Service\Command\CommandWriterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class CommandController extends AbstractController
{

    #[Route('/command', name: 'app_command')]
    public function showActiveCommand(#[CurrentUser]User $user, CommandReaderService $commandReader, CalculationService $calculationService): Response
    {
        $command = $commandReader->getUserActiveCommand($user);

        $totals = null;
        if (null !== $command) {
            $totals = $calculationService->getTolals($command);
        }
        

        return $this->render('command/index.html.twig', [
            'command' => $command,
            'totals' => $totals
        ]);
    }
    
    #[Route('/command/add/{id}', name: 'app_command_add')]
    public function addPructToCommand(Product $product, #[CurrentUser]User $user, CommandWriterService $commandWriter): Response
    {
        $commandWriter->addProductToCommand($product, $user);

        return $this->redirectToRoute('app_command');
    }

    #[Route('/command/valide/{id}', name: 'app_command_valide')]
    public function valideCommand(Command $command, #[CurrentUser]User $user, CommandWriterService $commandWriter): Response
    {
        $commandWriter->validateCommand($command, $user);

        return $this->redirectToRoute('app_command');
    }

    #[Route('/command/delete/{id}', name: 'app_command_delete')]
    public function deleteCommand(Command $command, CommandWriterService $commandWriterService): Response
    {
        $commandWriterService->deleteCommand($command);

        return $this->redirectToRoute('app_command');
    }
}
