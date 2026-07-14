<?php
namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Product> */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, Product::class); }

    /** @return Product[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.category', 'ASC')
            ->addOrderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Product[] */
    public function findActive(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.isActive = true')
            ->orderBy('p.category', 'ASC')
            ->addOrderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return Product[] */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :cat')
            ->andWhere('p.isActive = true')
            ->setParameter('cat', $category)
            ->orderBy('p.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySlug(string $slug): ?Product
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Returns products grouped by category for megamenu
     * @return array<string, Product[]>
     */
    public function findGroupedByCategory(): array
    {
        $products = $this->findActive();
        $grouped = [];
        foreach ($products as $product) {
            $grouped[$product->getCategory()][] = $product;
        }
        return $grouped;
    }
}
