<?php

namespace App\Repository;

use App\Entity\StandardItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StandardItem>
 */
class StandardItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StandardItem::class);
    }

    /**
     * @return StandardItem[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.isActive = :val')
            ->setParameter('val', true)
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return StandardItem[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.position', 'ASC')
            ->addOrderBy('s.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
