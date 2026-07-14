<?php
namespace App\Repository;

use App\Entity\Subproduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Subproduct> */
class SubproductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Subproduct::class); }

    /** @return Subproduct[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Subproduct[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = true')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
