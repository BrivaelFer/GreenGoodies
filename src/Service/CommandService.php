<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CommandService
{
    public function addProductToCommand(Product $product, User $user, EntityManagerInterface $entityManager): void
    {
        $command = $this->getUserActiveCommand($user);

        if (!$command) {
            $command = new Command();
            $command->setUser($user);
            $entityManager->persist($command);
        }

        $commandLine = $this->getCommandLineByProduct($command, $product);

        if (!$commandLine) {
            $commandLine = new CommandLine();
            $commandLine->setCommand($command);
            $commandLine->setProduct($product);
            $commandLine->setPrice($product->getPrice());
        }

        $commandLine->setQuantity(($commandLine->getQuantity() ?? 0) + 1);

        $entityManager->persist($commandLine);
        $entityManager->flush();
    }

    public function validateCommand(Command $command, EntityManagerInterface $entityManager): void
    {
        $command->setStatus(Command::STATUS_VALIDATED);
        $entityManager->persist($command);
        $entityManager->flush();
    }


    public function getTolals(Command $command): array
    {
        $calculationService = new CalculationService();
        $lines = [];

        foreach ($command->getCommandLines() as $commandLine) {
            $lines[$commandLine->getId()] = ($calculationService)->calculateLineTotal($commandLine);
        }

        return [
            'lines'=>$lines,
            'total'=>$calculationService->calculateCommandTotal($command)
        ];
    }

    public function getUserActiveCommand(User $user): ?Command
    {
        foreach ($user->getCommands() as $command) {
            if ($command->getStatus() === Command::STATUS_ACTIVE) {
                return $command;
            }
        }

        return null;
    }

    private function getCommandLineByProduct(Command $command, Product $product): ?CommandLine
    {
        foreach ($command->getCommandLines() as $commandLine) {
            if ($commandLine->getProduct()->getId() === $product->getId()) {
                return $commandLine;
            }
        }

        return null;
    }
}