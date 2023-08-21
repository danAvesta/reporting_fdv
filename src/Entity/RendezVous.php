<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Column]
    // private ?int $IdUser = null;

    #[ORM\Column(length: 255)]
    private ?string $NomEnseigne = null;

    #[ORM\Column(length: 255)]
    private ?string $Ville = null;

    #[ORM\Column]
    private ?int $CodePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $ContactNom = null;

    #[ORM\Column(length: 255)]
    private ?string $ContactNumero = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateRdv = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateUpdate = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $commercial = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\OneToMany(mappedBy: 'Idrdv', targetEntity: Formulairerdv::class)]
    private Collection $formulairerdvs;

    #[ORM\OneToMany(mappedBy: 'IdRdv', targetEntity: Inventairerdv::class)]
    private Collection $inventairerdvs;

    public function __construct()
    {
        $this->formulairerdvs = new ArrayCollection();
        $this->inventairerdvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

 
        public function getNomEnseigne(): ?string
    {
        return $this->NomEnseigne;
    }

    public function setNomEnseigne(string $NomEnseigne): static
    {
        $this->NomEnseigne = $NomEnseigne;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): static
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->CodePostal;
    }

    public function setCodePostal(int $CodePostal): static
    {
        $this->CodePostal = $CodePostal;

        return $this;
    }

    public function getContactNom(): ?string
    {
        return $this->ContactNom;
    }

    public function setContactNom(string $ContactNom): static
    {
        $this->ContactNom = $ContactNom;

        return $this;
    }

    public function getContactNumero(): ?string
    {
        return $this->ContactNumero;
    }

    public function setContactNumero(string $ContactNumero): static
    {
        $this->ContactNumero = $ContactNumero;

        return $this;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->DateRdv;
    }

    public function setDateRdv(\DateTimeInterface $DateRdv): static
    {
        $this->DateRdv = $DateRdv;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->DateCreation;
    }

    public function setDateCreation(\DateTimeInterface $DateCreation): static
    {
        $this->DateCreation = $DateCreation;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->DateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $DateUpdate): static
    {
        $this->DateUpdate = $DateUpdate;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Formulairerdv>
     */
    public function getFormulairerdvs(): Collection
    {
        return $this->formulairerdvs;
    }

    public function addFormulairerdv(Formulairerdv $formulairerdv): static
    {
        if (!$this->formulairerdvs->contains($formulairerdv)) {
            $this->formulairerdvs->add($formulairerdv);
            $formulairerdv->setIdrdv($this);
        }

        return $this;
    }

    public function removeFormulairerdv(Formulairerdv $formulairerdv): static
    {
        if ($this->formulairerdvs->removeElement($formulairerdv)) {
            // set the owning side to null (unless already changed)
            if ($formulairerdv->getIdrdv() === $this) {
                $formulairerdv->setIdrdv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Inventairerdv>
     */
    public function getInventairerdvs(): Collection
    {
        return $this->inventairerdvs;
    }

    public function addInventairerdv(Inventairerdv $inventairerdv): static
    {
        if (!$this->inventairerdvs->contains($inventairerdv)) {
            $this->inventairerdvs->add($inventairerdv);
            $inventairerdv->setIdRdv($this);
        }

        return $this;
    }

    public function removeInventairerdv(Inventairerdv $inventairerdv): static
    {
        if ($this->inventairerdvs->removeElement($inventairerdv)) {
            // set the owning side to null (unless already changed)
            if ($inventairerdv->getIdRdv() === $this) {
                $inventairerdv->setIdRdv(null);
            }
        }

        return $this;
    }

   
    



}
