<?php

namespace App\Repository;

use App\Entity\ServiceHeader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceHeader>
 */
class ServiceHeaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceHeader::class);
    }

    public function findOrCreate(): ServiceHeader
    {
        $entity = $this->findOneBy([]);
        if (!$entity) {
            $entity = new ServiceHeader();
            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
}
