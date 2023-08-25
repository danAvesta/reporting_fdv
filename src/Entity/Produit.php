<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ItemName = null;

    #[ORM\Column(length: 15)]
    private ?string $Ean = null;    

    #[ORM\Column(length: 500)]
    private ?string $Description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemName(): ?string
    {
        return $this->ItemName;
    }

    public function setItemName(string $ItemName): static
    {
        $this->ItemName = $ItemName;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->Ean;
    }

    public function setEan(string $Ean): static
    {
        $this->Ean = $Ean;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }
    public function __toString(): string
    {
        return $this->getItemName().' / '.$this->getDescription();
    }
    
   



}
