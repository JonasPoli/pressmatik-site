<?php
namespace App\Repository;

use App\Entity\Supplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Supplier> */
class SupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Supplier::class); }

    /** @return Supplier[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Supplier[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = true')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
