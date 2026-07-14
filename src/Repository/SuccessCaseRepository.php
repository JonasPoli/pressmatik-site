<?php
namespace App\Repository;

use App\Entity\SuccessCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<SuccessCase> */
class SuccessCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, SuccessCase::class); }

    /** @return SuccessCase[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return SuccessCase[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.isActive = true')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
