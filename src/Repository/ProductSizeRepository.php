<?php
namespace App\Repository;
use App\Entity\ProductSize;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProductSize> */
class ProductSizeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProductSize::class); }

    /** @return ProductSize[] */
    public function findBySubproductOrdered(int $subproductId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.subproduct = :subId')
            ->setParameter('subId', $subproductId)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
