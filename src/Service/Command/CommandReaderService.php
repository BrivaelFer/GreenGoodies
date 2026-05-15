<?php declare(strict_types=1);

namespace App\Service\Command;

use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\User;

final class CommandReaderService
{

    /**
     * Récupère la Command active de $user, si elle existe
     * @param User $user
     * @return Command|null
     */
    public function getUserActiveCommand(User $user): ?Command
    {
        foreach ($user->getCommands() as $command) {
            if ($command->getStatus() === Command::STATUS_ACTIVE) {
                return $command;
            }
        }

        return null;
    }

    /**
     * Récupère la CommandLine de $command qui correspont à $product
     * @param Command $command
     * @param Product $product
     * @return CommandLine|null
     */
    public function getCommandLineByProduct(Command $command, Product $product): ?CommandLine
    {
        foreach ($command->getCommandLines() as $commandLine) {
            if ($commandLine->getProduct()->getId() === $product->getId()) {
                return $commandLine;
            }
        }

        return null;
    }
}