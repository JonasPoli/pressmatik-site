<?php

namespace App\Entity;

use App\Repository\AboutGalleryImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: AboutGalleryImageRepository::class)]
#[Vich\Uploadable]
class AboutGalleryImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'galleryImages')]
    #[ORM\JoinColumn(nullable: true)]
    private ?AboutUs $aboutUs = null;

    #[Vich\UploadableField(mapping: 'gallery', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionEs = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCaption(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->captionEn,
            'es' => $this->captionEs,
            default => $this->captionPt,
        };
    }

    public function getId(): ?int { return $this->id; }

    public function getAboutUs(): ?AboutUs { return $this->aboutUs; }
    public function setAboutUs(?AboutUs $aboutUs): static { $this->aboutUs = $aboutUs; return $this; }

    public function setImageFile(?File $file = null): void
    {
        $this->imageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getImageFile(): ?File { return $this->imageFile; }
    public function setImageName(?string $v): void { $this->imageName = $v; }
    public function getImageName(): ?string { return $this->imageName; }

    public function getCaptionPt(): ?string { return $this->captionPt; }
    public function setCaptionPt(?string $v): static { $this->captionPt = $v; return $this; }
    public function getCaptionEn(): ?string { return $this->captionEn; }
    public function setCaptionEn(?string $v): static { $this->captionEn = $v; return $this; }
    public function getCaptionEs(): ?string { return $this->captionEs; }
    public function setCaptionEs(?string $v): static { $this->captionEs = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
}
