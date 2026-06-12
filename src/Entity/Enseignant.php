<?php

namespace App\Entity;

use App\Repository\EnseignantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnseignantRepository::class)]
class Enseignant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\ManyToOne(inversedBy: 'enseignants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $compte = null;

    /** @var Collection<int, Soutenance> */
    #[ORM\OneToMany(targetEntity: Soutenance::class, mappedBy: 'president')]
    private Collection $soutenancesPresident;

    /** @var Collection<int, Soutenance> */
    #[ORM\OneToMany(targetEntity: Soutenance::class, mappedBy: 'examinateur')]
    private Collection $soutenancesExaminateur;

    /** @var Collection<int, Soutenance> */
    #[ORM\OneToMany(targetEntity: Soutenance::class, mappedBy: 'encadreur')]
    private Collection $soutenancesEncadreur;

    public function __construct()
    {
        $this->soutenancesPresident   = new ArrayCollection();
        $this->soutenancesExaminateur = new ArrayCollection();
        $this->soutenancesEncadreur   = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;
        return $this;
    }

    public function getCompte(): ?User
    {
        return $this->compte;
    }

    public function setCompte(?User $compte): static
    {
        $this->compte = $compte;
        return $this;
    }

    /** @return Collection<int, Soutenance> */
    public function getSoutenancesPresident(): Collection
    {
        return $this->soutenancesPresident;
    }

    /** @return Collection<int, Soutenance> */
    public function getSoutenancesExaminateur(): Collection
    {
        return $this->soutenancesExaminateur;
    }

    /** @return Collection<int, Soutenance> */
    public function getSoutenancesEncadreur(): Collection
    {
        return $this->soutenancesEncadreur;
    }
}
