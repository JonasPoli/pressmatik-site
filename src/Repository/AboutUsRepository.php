<?php
namespace App\Repository;
use App\Entity\AboutUs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AboutUs> */
class AboutUsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AboutUs::class); }

    public function findOrCreate(): AboutUs
    {
        $entity = $this->findOneBy([]);
        if (!$entity) {
            $entity = new AboutUs();
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
}
