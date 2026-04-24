<?php declare(strict_types=1);

namespace App\Service\Command;

use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CalculationService;
use Exception;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CommandWriterService
{
    public function __construct(private EntityManagerInterface $entityManager) {
    }
    public function addProductToCommand(Product $product, User $user): void
    {
        $commandReaderService = new CommandReaderService();
        $command = $commandReaderService->getUserActiveCommand($user);

        if (!$command) {
            $command = new Command();
            $command->setUser($user);
            $command->setCreationDate(new \DateTime());
            $command->setStatus(Command::STATUS_ACTIVE);
            $this->entityManager->persist($command);
        }

        $commandLine = $commandReaderService->getCommandLineByProduct($command, $product);

        if (!$commandLine) {
            $commandLine = new CommandLine();
            $commandLine->setCommand($command);
            $commandLine->setProduct($product);
            $commandLine->setPrice($product->getPrice());
        }

        $commandLine->setQuantity(($commandLine->getQuantity() ?? 0) + 1);

        $this->entityManager->persist($commandLine);
        $this->entityManager->flush();
    }

    public function validateCommand(Command $command, User $user): bool
    {
        if($user->getId() === $command->getUser()->getId()) {
            $command->setStatus(Command::STATUS_VALIDATED);
            $this->entityManager->persist($command);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function deleteCommand(Command $command): void
    {
        $this->entityManager->remove($command);
        $this->entityManager->flush();
    }
}