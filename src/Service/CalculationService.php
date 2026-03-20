<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use App\Entity\CommandLine;

class CalculationService
{
    public function calculateCommandTotal(Command $command): float
    {
        $total = 0;

        foreach ($command->getCommandLines() as $commandLine) {
            $total += $this->calculateLineTotal($commandLine, true);
        }

        return $total * 0.01;
    }
    public function calculateLineTotal(CommandLine $commandLine, bool $int = false): float|int
    {
        return ($this->toInt($commandLine->getPrice()) * $commandLine->getQuantity())* ($int ? 1 : 0.01);
    }

    private function toInt(float $price): int
    {
        return (int)($price * 100);
    }
}