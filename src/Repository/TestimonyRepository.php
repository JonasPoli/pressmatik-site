<?php
namespace App\Repository;

use App\Entity\Testimony;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Testimony> */
class TestimonyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Testimony::class); }

    /** @return Testimony[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Testimony[] */
    public function findActiveOrdered(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('t.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
