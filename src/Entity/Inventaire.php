<?php

namespace App\Entity;

use App\Repository\InventaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventaireRepository::class)]
class Inventaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateInventaire = null;

    #[ORM\ManyToOne(inversedBy: 'inventaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rdv $idRdv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateInventaire(): ?\DateTimeInterface
    {
        return $this->dateInventaire;
    }

    public function setDateInventaire(\DateTimeInterface $dateInventaire): static
    {
        $this->dateInventaire = $dateInventaire;

        return $this;
    }

    public function getIdRdv(): ?Rdv
    {
        return $this->idRdv;
    }

    public function setIdRdv(?Rdv $idRdv): static
    {
        $this->idRdv = $idRdv;

        return $this;
    }
}
