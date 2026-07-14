<?php
namespace App\Repository;

use App\Entity\QualityCertification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<QualityCertification> */
class QualityCertificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, QualityCertification::class); }

    /** @return QualityCertification[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return QualityCertification[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.isActive = true')
            ->orderBy('q.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
