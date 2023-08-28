<?php

namespace App\Entity;

use App\Repository\RdvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RdvRepository::class)]
class Rdv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rdvs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateRdv = null;

    #[ORM\Column(length: 255)]
    private ?string $nomMagasin = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adresseMagasin = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'rdvs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $commercial = null;

    #[ORM\Column(length: 6)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $contactNom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactTel = null;

    #[ORM\OneToMany(mappedBy: 'idRdv', targetEntity: Inventaire::class)]
    private Collection $inventaires;

    public function __construct()
    {
        $this->inventaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreateBy(): ?User
    {
        return $this->createBy;
    }

    public function setCreateBy(?User $createBy): static
    {
        $this->createBy = $createBy;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): static
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->dateRdv;
    }

    public function setDateRdv(\DateTimeInterface $dateRdv): static
    {
        $this->dateRdv = $dateRdv;

        return $this;
    }

    public function getNomMagasin(): ?string
    {
        return $this->nomMagasin;
    }

    public function setNomMagasin(string $nomMagasin): static
    {
        $this->nomMagasin = $nomMagasin;

        return $this;
    }

    public function getAdresseMagasin(): ?string
    {
        return $this->adresseMagasin;
    }

    public function setAdresseMagasin(string $adresseMagasin): static
    {
        $this->adresseMagasin = $adresseMagasin;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCommercial(): ?User
    {
        return $this->commercial;
    }

    public function setCommercial(?User $commercial): static
    {
        $this->commercial = $commercial;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getContactNom(): ?string
    {
        return $this->contactNom;
    }

    public function setContactNom(string $contactNom): static
    {
        $this->contactNom = $contactNom;

        return $this;
    }

    public function getContactTel(): ?string
    {
        return $this->contactTel;
    }

    public function setContactTel(?string $contactTel): static
    {
        $this->contactTel = $contactTel;

        return $this;
    }

    /**
     * @return Collection<int, Inventaire>
     */
    public function getInventaires(): Collection
    {
        return $this->inventaires;
    }

    public function addInventaire(Inventaire $inventaire): static
    {
        if (!$this->inventaires->contains($inventaire)) {
            $this->inventaires->add($inventaire);
            $inventaire->setIdRdv($this);
        }

        return $this;
    }

    public function removeInventaire(Inventaire $inventaire): static
    {
        if ($this->inventaires->removeElement($inventaire)) {
            // set the owning side to null (unless already changed)
            if ($inventaire->getIdRdv() === $this) {
                $inventaire->setIdRdv(null);
            }
        }

        return $this;
    }
}
