<?php
namespace App\Repository;
use App\Entity\ProductVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProductVideo> */
class ProductVideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProductVideo::class); }

    /** @return ProductVideo[] */
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
