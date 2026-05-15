<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use App\Entity\CommandLine;

class CalculationService
{
    /**
     * Calcule les totaux de chacun produit différent, et de la command global
     * @param Command $command
     * @return array{lines: array<int, float>, total: float}
     */
    public function getTolals(Command $command): array
    {
        $lines = [];

        foreach ($command->getCommandLines() as $commandLine) {
            $lines[$commandLine->getId()] = $this->calculateLineTotal($commandLine);
        }

        return [
            'lines'=>$lines,
            'total'=>$this->calculateCommandTotal($command)
        ];
    }

    /**
     * Calcule le prix total d'une Command
     * @param Command $command
     * @return float|int
     */
    public function calculateCommandTotal(Command $command): float
    {
        $total = 0;

        foreach ($command->getCommandLines() as $commandLine) {
            $total += $this->calculateLineTotal($commandLine, true);
        }

        return $total * 0.01;
    }

    /**
     * Calcule le prix total d'CommandLine
     * @param CommandLine $commandLine
     * @param bool $int
     * @return float|int
     */
    public function calculateLineTotal(CommandLine $commandLine, bool $int = false): float|int
    {
        return ($this->toInt($commandLine->getPrice()) * $commandLine->getQuantity())* ($int ? 1 : 0.01);
    }

    /**
     * Converti un float en int
     * @param float $price
     * @return int
     */
    private function toInt(float $price): int
    {
        return (int)($price * 100);
    }
}