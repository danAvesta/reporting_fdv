<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Inventairerdv;
use App\Entity\RendezVous;

#[ORM\Entity]
class InventoryItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?int $quantity = null;
    
    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\ManyToOne(targetEntity: Inventairerdv::class, inversedBy: "items")]
    private ?Inventairerdv $inventairerdv = null;

    #[ORM\ManyToOne(targetEntity: RendezVous::class)]
    private ?RendezVous $Idrdv = null;

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getInventairerdv(): ?Inventairerdv
    {
        return $this->inventairerdv;
    }

    // Setters
    public function setReference(?string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setInventairerdv(?Inventairerdv $inventairerdv): self
    {
        $this->inventairerdv = $inventairerdv;
        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function getIdrdv(): ?RendezVous
    {
        return $this->Idrdv;
    }

    public function setIdrdv(?RendezVous $Idrdv): self
    {
        $this->Idrdv = $Idrdv;
        return $this;
    }
}
