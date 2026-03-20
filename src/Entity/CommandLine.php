<?php

namespace App\Entity;

use App\Repository\CommandLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandLineRepository::class)]
class CommandLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'commandLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Command $command = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Prix unitaire du produit au moment de la commande, en int pour les calcules, et en float pour l'affichage
     * @param bool $float détermine si le prix doit être retourné en float ou en int
     * @return float|int|null
     */
    public function getPrice(bool $float = false): int|float|null
    {
        if($float){
            return $this->price !== null ? (float)($this->price / 100) : null;
        }
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCommand(): ?Command
    {
        return $this->command;
    }

    public function setCommand(?Command $command): static
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Prix total la ligne, en int pour les calcules, et en float pour l'affichage
     * @param bool $float détermine si le prix doit être retourné en float ou en int
     * @return float|int|null
     */
    public function getTotalPrice(bool $float = false): int|float|null
    {
        if($this->price === null || $this->quantity === null){
            return null;
        }
        $total = $this->price * $this->quantity;
        return $float ? (float)($total / 100) : $total;
    }
}
