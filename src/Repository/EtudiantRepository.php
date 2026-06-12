<?php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Etudiant>
 */
class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    /** @return Etudiant[] */
    public function findAllOrderedByNom(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.nom', 'ASC')
            ->addOrderBy('e.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Etudiant[] */
    public function findBySearch(string $search): array
    {
        $term = '%' . $search . '%';

        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :term OR e.prenom LIKE :term OR e.email LIKE :term')
            ->setParameter('term', $term)
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les étudiants sans soutenance (+ optionnellement l'étudiant actuel).
     *
     * @return Etudiant[]
     */
    public function findSansSoutenance(?int $excludeEtudiantId = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.soutenances', 's')
            ->where('s.id IS NULL')
            ->orderBy('e.nom', 'ASC');

        if ($excludeEtudiantId !== null) {
            $qb->orWhere('e.id = :excludeId')
               ->setParameter('excludeId', $excludeEtudiantId);
        }

        return $qb->getQuery()->getResult();
    }
}
