<?php
namespace App\Repository;

use App\Entity\News;
use App\Entity\NewsCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<News> */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, News::class); }

    /** @return News[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return News[] */
    public function findActiveOrdered(?NewsCategory $category = null): array
    {
        $qb = $this->createQueryBuilder('n')
            ->where('n.isActive = :active')
            ->setParameter('active', true);

        if ($category) {
            $qb->join('n.categories', 'c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $category->getId());
        }

        return $qb->orderBy('n.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return News[] */
    public function findLatestActive(int $limit): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('n.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySlug(string $slug, string $locale): ?News
    {
        return $this->createQueryBuilder('n')
            ->where('n.isActive = :active')
            ->andWhere('n.slugPt = :slug OR n.slugEn = :slug OR n.slugEs = :slug')
            ->setParameter('active', true)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
