<?php

namespace App\Repository;

use App\Entity\Salle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Salle>
 */
class SalleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Salle::class);
    }

    /** @return Salle[] */
    public function findAllOrderedByCode(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.code', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCodeExcept(string $code, ?int $excludeId = null): ?Salle
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.code = :code')
            ->setParameter('code', $code);

        if ($excludeId !== null) {
            $qb->andWhere('s.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
