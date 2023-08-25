<?php

namespace App\Entity;

use App\Repository\InventairerdvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventairerdvRepository::class)]
class Inventairerdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id", nullable: false)]
    private ?Produit $Product = null;

    #[ORM\Column]
    private ?int $Quantite = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\ManyToOne(inversedBy: 'inventairerdvs')]
    private ?RendezVous $IdRdv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Produit
    {
        return $this->Product;
    }

    public function setProduct(?Produit $Product): self
    {
        $this->Product = $Product;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): self
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getIdRdv(): ?RendezVous
    {
        return $this->IdRdv;
    }

    public function setIdRdv(?RendezVous $IdRdv): self
    {
        $this->IdRdv = $IdRdv;

        return $this;
    }
}
