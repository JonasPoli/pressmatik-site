<?php

namespace App\Entity;

use App\Repository\NewsImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: NewsImageRepository::class)]
#[Vich\Uploadable]
class NewsImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: News::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?News $news = null;

    #[Vich\UploadableField(mapping: 'gallery', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $captionEs = null;

    #[ORM\Column]
    private int $position = 0;

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getCaption(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->captionEn ?: $this->captionPt,
            'es' => $this->captionEs ?: $this->captionPt,
            default => $this->captionPt,
        };
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getNews(): ?News { return $this->news; }
    public function setNews(?News $news): static { $this->news = $news; return $this; }

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

    public function getCaptionPt(): ?string { return $this->captionPt; }
    public function setCaptionPt(?string $v): static { $this->captionPt = $v; return $this; }
    public function getCaptionEn(): ?string { return $this->captionEn; }
    public function setCaptionEn(?string $v): static { $this->captionEn = $v; return $this; }
    public function getCaptionEs(): ?string { return $this->captionEs; }
    public function setCaptionEs(?string $v): static { $this->captionEs = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
