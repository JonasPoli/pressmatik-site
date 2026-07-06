<?php
namespace App\Repository;
use App\Entity\AboutGalleryImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<AboutGalleryImage> */
class AboutGalleryImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, AboutGalleryImage::class); }
}
