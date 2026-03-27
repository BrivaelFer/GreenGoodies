<?php declare(strict_types=1);

namespace App\Service\Command;

use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\User;
use App\Service\CalculationService;

final class CommandReaderService
{

    public function getUserActiveCommand(User $user): ?Command
    {
        foreach ($user->getCommands() as $command) {
            if ($command->getStatus() === Command::STATUS_ACTIVE) {
                return $command;
            }
        }

        return null;
    }

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