<?php

namespace App\Entity;

use App\Repository\BannerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: BannerRepository::class)]
#[Vich\Uploadable]
class Banner
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonTextPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonTextEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $buttonTextEs = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $buttonUrl = null;

    #[Vich\UploadableField(mapping: 'banners', fileNameProperty: 'imageName')]
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

    public function getSubtitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->subtitleEn ?: $this->subtitlePt,
            'es' => $this->subtitleEs ?: $this->subtitlePt,
            default => $this->subtitlePt,
        };
    }

    public function getButtonText(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->buttonTextEn ?: $this->buttonTextPt,
            'es' => $this->buttonTextEs ?: $this->buttonTextPt,
            default => $this->buttonTextPt,
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

    public function getSubtitlePt(): ?string { return $this->subtitlePt; }
    public function setSubtitlePt(?string $v): static { $this->subtitlePt = $v; return $this; }
    public function getSubtitleEn(): ?string { return $this->subtitleEn; }
    public function setSubtitleEn(?string $v): static { $this->subtitleEn = $v; return $this; }
    public function getSubtitleEs(): ?string { return $this->subtitleEs; }
    public function setSubtitleEs(?string $v): static { $this->subtitleEs = $v; return $this; }

    public function getButtonTextPt(): ?string { return $this->buttonTextPt; }
    public function setButtonTextPt(?string $v): static { $this->buttonTextPt = $v; return $this; }
    public function getButtonTextEn(): ?string { return $this->buttonTextEn; }
    public function setButtonTextEn(?string $v): static { $this->buttonTextEn = $v; return $this; }
    public function getButtonTextEs(): ?string { return $this->buttonTextEs; }
    public function setButtonTextEs(?string $v): static { $this->buttonTextEs = $v; return $this; }

    public function getButtonUrl(): ?string { return $this->buttonUrl; }
    public function setButtonUrl(?string $v): static { $this->buttonUrl = $v; return $this; }

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
