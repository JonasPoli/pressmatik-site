<?php

namespace App\Repository;

use App\Entity\OptionalItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OptionalItem>
 */
class OptionalItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OptionalItem::class);
    }

    /**
     * @return OptionalItem[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.isActive = :val')
            ->setParameter('val', true)
            ->orderBy('o.position', 'ASC')
            ->addOrderBy('o.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return OptionalItem[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.position', 'ASC')
            ->addOrderBy('o.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
