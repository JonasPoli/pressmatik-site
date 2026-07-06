<?php
namespace App\Repository;
use App\Entity\HistoryTimeline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<HistoryTimeline> */
class HistoryTimelineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, HistoryTimeline::class); }

    /** @return HistoryTimeline[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('h')
            ->orderBy('h.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
