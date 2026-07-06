<?php
namespace App\Repository;

use App\Entity\ClientLogo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ClientLogo> */
class ClientLogoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ClientLogo::class); }

    /** @return ClientLogo[] */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
