<?php

namespace App\Repository;

use App\Entity\MegaMenuCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MegaMenuCategory>
 */
class MegaMenuCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MegaMenuCategory::class);
    }

    /**
     * @return MegaMenuCategory[]
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByKey(string $key): ?MegaMenuCategory
    {
        return $this->findOneBy(['categoryKey' => $key]);
    }
}
