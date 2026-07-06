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
    public function findBySlugOrdered(string $slug): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.productSlug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('v.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
