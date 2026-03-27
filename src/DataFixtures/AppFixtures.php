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
        $datas = [
            'users' => [
                [
                    'email' => 'admin@greengoodies.com',
                    'name' => 'Dupont',
                    'firstName' => 'Admin',
                    'roles' => ['ROLE_ADMIN'],
                    'password' => 'admin123'
                ],
                [
                    'email' => 'user@greengoodies.com',
                    'name' => 'Martin',
                    'firstName' => 'Jean',
                    'roles' => ['ROLE_USER'],
                    'password' => 'user123'
                ],
                [
                    'email' => 'marie@greengoodies.com',
                    'name' => 'Bernard',
                    'firstName' => 'Marie',
                    'roles' => ['ROLE_USER'],
                    'password' => 'marie123'
                ]
            ],
            'products' => [
                [
                    'label' => 'Bouteille Réutilisable',
                    'img' => 'bottle.jpg',
                    'description' => 'Bouteille écologique fabriquée en matériau durable',
                    'lightDescription' => 'Bouteille réutilisable eco-friendly',
                    'enable' => true,
                    'price' => 25.99
                ],
                [
                    'label' => 'Sac à Provisions Biodégradable',
                    'img' => 'bag.jpg',
                    'description' => 'Sac biodégradable résistant et respectueux de l\'environnement',
                    'lightDescription' => 'Sac écologique biodégradable',
                    'enable' => true,
                    'price' => 12.50
                ],
                [
                    'label' => 'Brosse à Dents en Bambou',
                    'img' => 'toothbrush.jpg',
                    'description' => 'Brosse à dents naturelle en bambou avec poils compostables',
                    'lightDescription' => 'Brosse à dents écologique en bambou',
                    'enable' => true,
                    'price' => 4.99
                ],
                [
                    'label' => 'Savon Naturel Bio',
                    'img' => 'soap.jpg',
                    'description' => 'Savon 100% naturel et bio sans chimie agressive',
                    'lightDescription' => 'Savon naturel bio premium',
                    'enable' => true,
                    'price' => 6.50
                ]
            ],
            'commands' => [
                [
                    'user' => 0,
                    'creationDate' => 'now',
                    'status' => 'active'
                ],
                [
                    'user' => 1,
                    'creationDate' => '2026-02-20 14:45:00',
                    'status' => 'validated'
                ],
                [
                    'user' => 2,
                    'creationDate' => 'now',
                    'status' => 'active'
                ]
            ],
            'commandLines' => [
                [
                    'command' => 0,
                    'product' => 0,
                    'quantity' => 2
                ],
                [
                    'command' => 0,
                    'product' => 2,
                    'quantity' => 3
                ],
                [
                    'command' => 1,
                    'product' => 1,
                    'quantity' => 1
                ],
                [
                    'command' => 1,
                    'product' => 3,
                    'quantity' => 2
                ],
                [
                    'command' => 2,
                    'product' => 0,
                    'quantity' => 1
                ],
                [
                    'command' => 2,
                    'product' => 1,
                    'quantity' => 4
                ]
            ]
        ];

        foreach($this->makeEntitys($datas) as $entityGroup) {
            foreach($entityGroup as $entity) {
                $manager->persist($entity);
            }
        }

        $manager->flush();
    }

    /**
     * Génère les entités
     * @param array $datas
     * @return array<array>
     */
    private function makeEntitys(array $datas): array
    {
        $users = $this->makeUsers($datas['users']); 
        $products = $this->makeProducts($datas['products']);

        $commands = $this->makeCommands($datas['commands'], $users);
        $commandLines = $this->makeCommandLines($datas['commandLines'], $commands, $products);

        return [$users, $products, $commands, $commandLines];
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
            $user->setApiEnable(false);
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
