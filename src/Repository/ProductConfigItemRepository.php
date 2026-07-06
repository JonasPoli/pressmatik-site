<?php
namespace App\Repository;
use App\Entity\ProductConfigItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ProductConfigItem> */
class ProductConfigItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ProductConfigItem::class); }

    /** @return ProductConfigItem[] */
    public function findBySlugAndType(string $slug, string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.productSlug = :slug')
            ->andWhere('c.type = :type')
            ->setParameter('slug', $slug)
            ->setParameter('type', $type)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
