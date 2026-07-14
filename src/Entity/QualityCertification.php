<?php

namespace App\Entity;

use App\Repository\QualityCertificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: QualityCertificationRepository::class)]
#[Vich\Uploadable]
class QualityCertification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEs = null;

    #[Vich\UploadableField(mapping: 'quality', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getTitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->titleEn ?: $this->titlePt,
            'es' => $this->titleEs ?: $this->titlePt,
            default => $this->titlePt,
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

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }
}
