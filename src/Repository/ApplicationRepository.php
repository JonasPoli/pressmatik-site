<?php

namespace App\Repository;

use App\Entity\Application;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @return Application[] Returns an array of active Application objects ordered by position
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :val')
            ->setParameter('val', true)
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Application[] Returns an array of all Application objects ordered by position
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
