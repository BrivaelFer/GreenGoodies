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

    /**
     * Ajoute un $product à la Command active de $user. (Créer la Command si elle n'éxiste pas)
     * @param Product $product
     * @param User $user
     * @return void
     */
    public function addProductToCommand(Product $product, User $user): void
    {
        $commandReaderService = new CommandReaderService();
        $command = $commandReaderService->getUserActiveCommand($user);

        // Créer la Command si elle n'éxiste pas
        if (!$command) {
            $command = new Command();
            $command->setUser($user);
            $command->setCreationDate(new \DateTime());
            $command->setStatus(Command::STATUS_ACTIVE);
            $this->entityManager->persist($command);
        }

        $commandLine = $commandReaderService->getCommandLineByProduct($command, $product);

        // Créer la CommandLine si elle n'éxiste pas
        if (!$commandLine) {
            $commandLine = new CommandLine();
            $commandLine->setCommand($command);
            $commandLine->setProduct($product);
            $commandLine->setPrice($product->getPrice());
        }

        // Si la line existe ajoute 1 à la quantité
        $commandLine->setQuantity(($commandLine->getQuantity() ?? 0) + 1);

        $this->entityManager->persist($commandLine);
        $this->entityManager->flush();
    }

    /**
     * Valide la command si elle appartien à l'utilisateur
     * @param Command $command
     * @param User $user
     * @return bool
     */
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

    /**
     * Supprime $command si elle appartien à $user
     * @param Command $command
     * @param User $user
     * @return void
     */
    public function deleteCommand(Command $command, User $user): void
    {
        if($user->getId() === $command->getUser()->getId()) {
            $this->entityManager->remove($command);
            $this->entityManager->flush();
        }
    }
}