<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use App\Entity\User;
use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandController extends AbstractController
{
    #[Route('/command', name: 'app_command')]
    public function showActiveCommand(): Response
    {
        /** @var User */
        $user = $this->getUser();
        /** @var Command */
        $command = $user->getActiveCommand();

        return $this->render('command/index.html.twig', [
            'user' => $user,
            'command' => $command
        ]);
    }
    
    #[Route('/command/add/{id}', name: 'app_command_add')]
    public function addPructToCommand(Product $product, EntityManagerInterface $entityManager): Response
    {
        /** @var User */

        $user = $this->getUser();
        /** @var Command */
        $command = $user->getActiveCommand();
        if(!$command){
            $command = new Command();
            $command->setUser($user);
            $entityManager->persist($command);
        }

        $commandLine = $command->getCommandLineByProduct($product);

        if($commandLine == null){
            $commandLine = new CommandLine();
            $commandLine->setCommand($command);
            $commandLine->setProduct($product);
            $commandLine->setPrice($product->getPrice());
        }

        $commandLine->setQuantity($commandLine->getQuantity() ?? 0 + 1);

        $entityManager->persist($commandLine);
        $entityManager->flush();

        return $this->redirectToRoute('app_command');
    }

    #[Route('/command/valide/{id}', name: 'app_command_valide')]
    public function valideCommand(Command $command, EntityManagerInterface $entityManager): Response
    {
        $command->setStatus(Command::STATUS_VALIDATED);
        $entityManager->persist($command);
        $entityManager->flush();

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
