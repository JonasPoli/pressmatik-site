<?php
namespace App\Repository;

use App\Entity\Banner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Banner> */
class BannerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Banner::class); }

    /** @return Banner[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Banner[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.isActive = true')
            ->orderBy('b.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
