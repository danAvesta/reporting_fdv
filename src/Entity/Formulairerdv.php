<?php

namespace App\Entity;

use App\Repository\FormulairerdvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormulairerdvRepository::class)]
class Formulairerdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $TailleMagasin = null;

    #[ORM\ManyToOne(inversedBy: 'formulairerdvs')]
    private ?RendezVous $Idrdv = null;

    #[ORM\Column(length: 255)]
    private ?string $Marque = null;

    #[ORM\Column(length: 255)]
    private ?string $Display = null;

    #[ORM\Column(length: 255)]
    private ?string $Reference = null;

    #[ORM\Column(length: 255)]
    private ?string $Quantite = null;

    #[ORM\Column(length: 255)]
    private ?string $Plv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $MotifDeNonPresence = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $DemandeDinstalationPlv = null;

    #[ORM\Column(length: 255)]
    private ?string $FichePromo = null;

    #[ORM\Column(length: 255)]
    private ?string $RaisonNonPresenceFichePromo = null;

    #[ORM\Column]
    private ?int $RessentiDeLaVisite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $RemarqueEnPlus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTailleMagasin(): ?int
    {
        return $this->TailleMagasin;
    }

    public function setTailleMagasin(int $TailleMagasin): static
    {
        $this->TailleMagasin = $TailleMagasin;

        return $this;
    }

    public function getIdrdv(): ?RendezVous
    {
        return $this->Idrdv;
    }

    public function setIdrdv(?RendezVous $Idrdv): static
    {
        $this->Idrdv = $Idrdv;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->Marque;
    }

    public function setMarque(string $Marque): static
    {
        $this->Marque = $Marque;

        return $this;
    }

    public function getDisplay(): ?string
    {
        return $this->Display;
    }

    public function setDisplay(string $Display): static
    {
        $this->Display = $Display;

        return $this;
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

    public function getQuantite(): ?string
    {
        return $this->Quantite;
    }

    public function setQuantite(string $Quantite): static
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getPlv(): ?string
    {
        return $this->Plv;
    }

    public function setPlv(string $Plv): static
    {
        $this->Plv = $Plv;

        return $this;
    }

    public function getMotifDeNonPresence(): ?string
    {
        return $this->MotifDeNonPresence;
    }

    public function setMotifDeNonPresence(string $MotifDeNonPresence): static
    {
        $this->MotifDeNonPresence = $MotifDeNonPresence;

        return $this;
    }

    public function getDemandeDinstalationPlv(): ?string
    {
        return $this->DemandeDinstalationPlv;
    }

    public function setDemandeDinstalationPlv(string $DemandeDinstalationPlv): static
    {
        $this->DemandeDinstalationPlv = $DemandeDinstalationPlv;

        return $this;
    }

    public function getFichePromo(): ?string
    {
        return $this->FichePromo;
    }

    public function setFichePromo(string $FichePromo): static
    {
        $this->FichePromo = $FichePromo;

        return $this;
    }

    public function getRaisonNonPresenceFichePromo(): ?string
    {
        return $this->RaisonNonPresenceFichePromo;
    }

    public function setRaisonNonPresenceFichePromo(string $RaisonNonPresenceFichePromo): static
    {
        $this->RaisonNonPresenceFichePromo = $RaisonNonPresenceFichePromo;

        return $this;
    }

    public function getRessentiDeLaVisite(): ?int
    {
        return $this->RessentiDeLaVisite;
    }

    public function setRessentiDeLaVisite(int $RessentiDeLaVisite): static
    {
        $this->RessentiDeLaVisite = $RessentiDeLaVisite;

        return $this;
    }

    public function getRemarqueEnPlus(): ?string
    {
        return $this->RemarqueEnPlus;
    }

    public function setRemarqueEnPlus(?string $RemarqueEnPlus): static
    {
        $this->RemarqueEnPlus = $RemarqueEnPlus;

        return $this;
    }
}
