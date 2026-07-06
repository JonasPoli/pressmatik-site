<?php
namespace App\Repository;
use App\Entity\TechnicalSpecification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<TechnicalSpecification> */
class TechnicalSpecificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, TechnicalSpecification::class); }

    /** @return TechnicalSpecification[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
