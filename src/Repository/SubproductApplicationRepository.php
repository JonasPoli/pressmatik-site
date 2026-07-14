<?php

namespace App\Repository;

use App\Entity\SubproductApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SubproductApplication> */
class SubproductApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubproductApplication::class);
    }

    /** @return SubproductApplication[] */
    public function findBySubproductOrdered(int $subproductId): array
    {
        return $this->createQueryBuilder('sa')
            ->where('sa.subproduct = :subId')
            ->setParameter('subId', $subproductId)
            ->orderBy('sa.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
