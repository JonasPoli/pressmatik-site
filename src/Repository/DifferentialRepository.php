<?php
namespace App\Repository;

use App\Entity\Differential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Differential> */
class DifferentialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Differential::class); }

    /** @return Differential[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Differential[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.isActive = true')
            ->orderBy('d.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
