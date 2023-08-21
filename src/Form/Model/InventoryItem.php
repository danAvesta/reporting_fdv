<?php
namespace App\Form\Model;

class InventoryItem
{
    private ?string $reference = null;
    private ?int $quantity = null;

    // Getters
    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
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
}
