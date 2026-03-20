<?php

namespace App\DataFixtures;

use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}
    public function load(ObjectManager $manager): void
    {
        

        $manager->flush();
    }

    /**
     * Crée des utilisateurs de test
     * @param array $usersData
     * @return User[]
     */
    private function makeUsers(array $usersData): array
    {
        $users = [];
        foreach($usersData as $userData){
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setName($userData['name']);
            $user->setFirstName($userData['firstName']);
            $user->setRoles($userData['roles']);
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $userData['password']
            ));
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Crée des produits de test
     * @param array $productsData
     * @return Product[]
     */
    private function makeProducts(array $productsData): array
    {
        $products = [];
        foreach($productsData as $productData){
            $product = new Product();
            $product->setLabel($productData['label']);
            $product->setImg($productData['img']);
            $product->setDescription($productData['description']);
            $product->setLightDescription($productData['lightDescription']);
            $product->setEnable($productData['enable']);
            $product->setPrice($productData['price']);
            $products[] = $product;
        }
        return $products;
    }

    private function makeCommands(array $commandsData, array $users): array
    {
        $commends = [];
        foreach($commandsData as $commandData){
            $command = new Command();
            $command->setUser($users[$commandData['user']]);
            $command->setCreationDate(new \DateTime($commandData['creationDate']));
            $command->setStatus($commandData['status']);
            $commends[] = $command;
        }
        return $commends;
    }

    private function makeCommandLines(array $commandLinesData, array $commands, array $products): array
    {
        $commandLines = [];
        foreach($commandLinesData as $commandLineData){
            $commandLine = new CommandLine();
            $product = $products[$commandLineData['product']];
            $commandLine->setCommand($commands[$commandLineData['command']]);
            $commandLine->setProduct($product);
            $commandLine->setQuantity($commandLineData['quantity']);
            $commandLine->setPrice($product->getPrice());
            $commandLines[] = $commandLine;
        }
        return $commandLines;
    }


}
