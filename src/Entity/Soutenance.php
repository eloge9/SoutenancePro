<?php

namespace App\Entity;

use App\Repository\SoutenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoutenanceRepository::class)]
class Soutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure = null;

    #[ORM\ManyToOne(inversedBy: 'soutenances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne(inversedBy: 'soutenances')]
    private ?Salle $salle = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesPresident')]
    private ?Enseignant $president = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesExaminateur')]
    private ?Enseignant $examinateur = null;

    #[ORM\ManyToOne(inversedBy: 'soutenancesEncadreur')]
    private ?Enseignant $encadreur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?\DateTime
    {
        return $this->heure;
    }

    public function setHeure(\DateTime $heure): static
    {
        $this->heure = $heure;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): static
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getPresident(): ?Enseignant
    {
        return $this->president;
    }

    public function setPresident(?Enseignant $president): static
    {
        $this->president = $president;

        return $this;
    }

    public function getExaminateur(): ?Enseignant
    {
        return $this->examinateur;
    }

    public function setExaminateur(?Enseignant $examinateur): static
    {
        $this->examinateur = $examinateur;

        return $this;
    }

    public function getEncadreur(): ?Enseignant
    {
        return $this->encadreur;
    }

    public function setEncadreur(?Enseignant $encadreur): static
    {
        $this->encadreur = $encadreur;

        return $this;
    }
}
