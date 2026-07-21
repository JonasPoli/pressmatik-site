<?php

namespace App\Repository;

use App\Entity\ApplicationListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApplicationListItem>
 */
class ApplicationListItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApplicationListItem::class);
    }

    /**
     * @return ApplicationListItem[]
     */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.isActive = :val')
            ->setParameter('val', true)
            ->orderBy('a.position', 'ASC')
            ->addOrderBy('a.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return ApplicationListItem[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.position', 'ASC')
            ->addOrderBy('a.namePt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
