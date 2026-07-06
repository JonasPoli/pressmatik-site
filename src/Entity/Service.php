<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[Vich\Uploadable]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEs = null;

    #[Vich\UploadableField(mapping: 'services', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private int $position = 0;

    /** @var Collection<int, ServiceImage> */
    #[ORM\OneToMany(targetEntity: ServiceImage::class, mappedBy: 'service', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getTitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->titleEn ?: $this->titlePt,
            'es' => $this->titleEs ?: $this->titlePt,
            default => $this->titlePt,
        };
    }

    public function getSlug(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->slugEn ?: $this->slugPt,
            'es' => $this->slugEs ?: $this->slugPt,
            default => $this->slugPt,
        };
    }

    public function getShortDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->shortDescriptionEn ?: $this->shortDescriptionPt,
            'es' => $this->shortDescriptionEs ?: $this->shortDescriptionPt,
            default => $this->shortDescriptionPt,
        };
    }

    public function getDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->descriptionEn ?: $this->descriptionPt,
            'es' => $this->descriptionEs ?: $this->descriptionPt,
            default => $this->descriptionPt,
        };
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getTitlePt(): ?string { return $this->titlePt; }
    public function setTitlePt(?string $v): static { $this->titlePt = $v; return $this; }
    public function getTitleEn(): ?string { return $this->titleEn; }
    public function setTitleEn(?string $v): static { $this->titleEn = $v; return $this; }
    public function getTitleEs(): ?string { return $this->titleEs; }
    public function setTitleEs(?string $v): static { $this->titleEs = $v; return $this; }

    public function getSlugPt(): ?string { return $this->slugPt; }
    public function setSlugPt(?string $v): static { $this->slugPt = $v; return $this; }
    public function getSlugEn(): ?string { return $this->slugEn; }
    public function setSlugEn(?string $v): static { $this->slugEn = $v; return $this; }
    public function getSlugEs(): ?string { return $this->slugEs; }
    public function setSlugEs(?string $v): static { $this->slugEs = $v; return $this; }

    public function getShortDescriptionPt(): ?string { return $this->shortDescriptionPt; }
    public function setShortDescriptionPt(?string $v): static { $this->shortDescriptionPt = $v; return $this; }
    public function getShortDescriptionEn(): ?string { return $this->shortDescriptionEn; }
    public function setShortDescriptionEn(?string $v): static { $this->shortDescriptionEn = $v; return $this; }
    public function getShortDescriptionEs(): ?string { return $this->shortDescriptionEs; }
    public function setShortDescriptionEs(?string $v): static { $this->shortDescriptionEs = $v; return $this; }

    public function getDescriptionPt(): ?string { return $this->descriptionPt; }
    public function setDescriptionPt(?string $v): static { $this->descriptionPt = $v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $v): static { $this->descriptionEn = $v; return $this; }
    public function getDescriptionEs(): ?string { return $this->descriptionEs; }
    public function setDescriptionEs(?string $v): static { $this->descriptionEs = $v; return $this; }

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

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    /** @return Collection<int, ServiceImage> */
    public function getImages(): Collection { return $this->images; }

    public function addImage(ServiceImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setService($this);
        }
        return $this;
    }

    public function removeImage(ServiceImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getService() === $this) {
                $image->setService(null);
            }
        }
        return $this;
    }
}
