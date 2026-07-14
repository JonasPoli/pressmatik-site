<?php
namespace App\Repository;

use App\Entity\OrgChartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<OrgChartItem> */
class OrgChartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, OrgChartItem::class); }

    /** @return OrgChartItem[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
