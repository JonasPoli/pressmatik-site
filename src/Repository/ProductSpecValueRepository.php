<?php
namespace App\Repository;
use App\Entity\ProductSpecValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProductSpecValue> */
class ProductSpecValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProductSpecValue::class); }

    /** @return ProductSpecValue[] */
    public function findBySubproductOrdered(int $subproductId): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.subproduct = :subId')
            ->setParameter('subId', $subproductId)
            ->orderBy('v.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
