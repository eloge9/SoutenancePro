<?php

namespace App\Repository;

use App\Entity\Enseignant;
use App\Entity\Salle;
use App\Entity\Soutenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Soutenance>
 */
class SoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soutenance::class);
    }

    /** @return Soutenance[] */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Soutenance[] */
    public function findByDateString(string $date): array
    {
        $dt = new \DateTime($date);

        return $this->createQueryBuilder('s')
            ->where('s.date = :date')
            ->setParameter('date', $dt->format('Y-m-d'))
            ->orderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findConflitSalle(Salle $salle, \DateTime $date, \DateTime $heure, ?int $excludeId = null): ?Soutenance
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.salle = :salle')
            ->andWhere('s.date = :date')
            ->andWhere('s.heure = :heure')
            ->setParameter('salle', $salle)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('heure', $heure->format('H:i:s'));

        if ($excludeId !== null) {
            $qb->andWhere('s.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    public function findConflitJury(Enseignant $enseignant, \DateTime $date, \DateTime $heure, ?int $excludeId = null): ?Soutenance
    {
        $qb = $this->createQueryBuilder('s')
            ->where('(s.president = :ens OR s.examinateur = :ens OR s.encadreur = :ens)')
            ->andWhere('s.date = :date')
            ->andWhere('s.heure = :heure')
            ->setParameter('ens', $enseignant)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('heure', $heure->format('H:i:s'));

        if ($excludeId !== null) {
            $qb->andWhere('s.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    /** @return Soutenance[] */
    public function findByEnseignant(Enseignant $enseignant): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.president = :ens OR s.examinateur = :ens OR s.encadreur = :ens')
            ->setParameter('ens', $enseignant)
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByEnseignant(Enseignant $enseignant): int
    {
        return (int) $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->where('s.president = :ens OR s.examinateur = :ens OR s.encadreur = :ens')
            ->setParameter('ens', $enseignant)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
