<?php

namespace App\Entity;

use App\Repository\InventairerdvRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventairerdvRepository::class)]
class Inventairerdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Reference = null;

    #[ORM\Column]
    private ?int $Quantite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\ManyToOne(inversedBy: 'inventairerdvs')]
    private ?RendezVous $IdRdv = null;

    #[ORM\OneToMany(mappedBy: "inventairerdv", targetEntity: InventoryItem::class)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(InventoryItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setInventairerdv($this);
        }

        return $this;
    }

    public function removeItem(InventoryItem $item): static
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            if ($item->getInventairerdv() === $this) {
                $item->setInventairerdv(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->Reference;
    }

    public function setReference(string $Reference): static
    {
        $this->Reference = $Reference;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->Quantite;
    }

    public function setQuantite(int $Quantite): static
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getIdRdv(): ?RendezVous
    {
        return $this->IdRdv;
    }

    public function setIdRdv(?RendezVous $IdRdv): static
    {
        $this->IdRdv = $IdRdv;

        return $this;
    }
}
