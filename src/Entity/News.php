<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[Vich\Uploadable]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** @var Collection<int, NewsCategory> */
    #[ORM\ManyToMany(targetEntity: NewsCategory::class, inversedBy: 'news')]
    private Collection $categories;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $fullDescriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $fullDescriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $fullDescriptionEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeVideoCode = null;

    #[Vich\UploadableField(mapping: 'news', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slugEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoTitleEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoDescriptionPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoDescriptionEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $seoDescriptionEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageAltPt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageAltEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageAltEs = null;

    #[ORM\Column]
    private bool $isHighlighted = false;

    #[ORM\Column]
    private bool $isActive = true;

    /** @var Collection<int, NewsImage> */
    #[ORM\OneToMany(targetEntity: NewsImage::class, mappedBy: 'news', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $images;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->date = new \DateTime();
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

    public function getShortDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->shortDescriptionEn ?: $this->shortDescriptionPt,
            'es' => $this->shortDescriptionEs ?: $this->shortDescriptionPt,
            default => $this->shortDescriptionPt,
        };
    }

    public function getFullDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->fullDescriptionEn ?: $this->fullDescriptionPt,
            'es' => $this->fullDescriptionEs ?: $this->fullDescriptionPt,
            default => $this->fullDescriptionPt,
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

    public function getSeoTitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->seoTitleEn ?: $this->titleEn,
            'es' => $this->seoTitleEs ?: $this->titleEs,
            default => $this->seoTitlePt ?: $this->titlePt,
        };
    }

    public function getSeoDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->seoDescriptionEn ?: $this->shortDescriptionEn,
            'es' => $this->seoDescriptionEs ?: $this->shortDescriptionEs,
            default => $this->seoDescriptionPt ?: $this->shortDescriptionPt,
        };
    }

    public function getImageAlt(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->imageAltEn ?: $this->imageAltPt,
            'es' => $this->imageAltEs ?: $this->imageAltPt,
            default => $this->imageAltPt,
        };
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    /** @return Collection<int, NewsCategory> */
    public function getCategories(): Collection { return $this->categories; }

    public function addCategory(NewsCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }
        return $this;
    }

    public function removeCategory(NewsCategory $category): static
    {
        $this->categories->removeElement($category);
        return $this;
    }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(?\DateTimeInterface $v): static { $this->date = $v; return $this; }

    public function getTitlePt(): ?string { return $this->titlePt; }
    public function setTitlePt(?string $v): static { $this->titlePt = $v; return $this; }
    public function getTitleEn(): ?string { return $this->titleEn; }
    public function setTitleEn(?string $v): static { $this->titleEn = $v; return $this; }
    public function getTitleEs(): ?string { return $this->titleEs; }
    public function setTitleEs(?string $v): static { $this->titleEs = $v; return $this; }

    public function getShortDescriptionPt(): ?string { return $this->shortDescriptionPt; }
    public function setShortDescriptionPt(?string $v): static { $this->shortDescriptionPt = $v; return $this; }
    public function getShortDescriptionEn(): ?string { return $this->shortDescriptionEn; }
    public function setShortDescriptionEn(?string $v): static { $this->shortDescriptionEn = $v; return $this; }
    public function getShortDescriptionEs(): ?string { return $this->shortDescriptionEs; }
    public function setShortDescriptionEs(?string $v): static { $this->shortDescriptionEs = $v; return $this; }

    public function getFullDescriptionPt(): ?string { return $this->fullDescriptionPt; }
    public function setFullDescriptionPt(?string $v): static { $this->fullDescriptionPt = $v; return $this; }
    public function getFullDescriptionEn(): ?string { return $this->fullDescriptionEn; }
    public function setFullDescriptionEn(?string $v): static { $this->fullDescriptionEn = $v; return $this; }
    public function getFullDescriptionEs(): ?string { return $this->fullDescriptionEs; }
    public function setFullDescriptionEs(?string $v): static { $this->fullDescriptionEs = $v; return $this; }

    public function getYoutubeVideoCode(): ?string { return $this->youtubeVideoCode; }
    public function setYoutubeVideoCode(?string $v): static { $this->youtubeVideoCode = $v; return $this; }

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

    public function getSlugPt(): ?string { return $this->slugPt; }
    public function setSlugPt(?string $v): static { $this->slugPt = $v; return $this; }
    public function getSlugEn(): ?string { return $this->slugEn; }
    public function setSlugEn(?string $v): static { $this->slugEn = $v; return $this; }
    public function getSlugEs(): ?string { return $this->slugEs; }
    public function setSlugEs(?string $v): static { $this->slugEs = $v; return $this; }

    public function getSeoTitlePt(): ?string { return $this->seoTitlePt; }
    public function setSeoTitlePt(?string $v): static { $this->seoTitlePt = $v; return $this; }
    public function getSeoTitleEn(): ?string { return $this->seoTitleEn; }
    public function setSeoTitleEn(?string $v): static { $this->seoTitleEn = $v; return $this; }
    public function getSeoTitleEs(): ?string { return $this->seoTitleEs; }
    public function setSeoTitleEs(?string $v): static { $this->seoTitleEs = $v; return $this; }

    public function getSeoDescriptionPt(): ?string { return $this->seoDescriptionPt; }
    public function setSeoDescriptionPt(?string $v): static { $this->seoDescriptionPt = $v; return $this; }
    public function getSeoDescriptionEn(): ?string { return $this->seoDescriptionEn; }
    public function setSeoDescriptionEn(?string $v): static { $this->seoDescriptionEn = $v; return $this; }
    public function getSeoDescriptionEs(): ?string { return $this->seoDescriptionEs; }
    public function setSeoDescriptionEs(?string $v): static { $this->seoDescriptionEs = $v; return $this; }

    public function getImageAltPt(): ?string { return $this->imageAltPt; }
    public function setImageAltPt(?string $v): static { $this->imageAltPt = $v; return $this; }
    public function getImageAltEn(): ?string { return $this->imageAltEn; }
    public function setImageAltEn(?string $v): static { $this->imageAltEn = $v; return $this; }
    public function getImageAltEs(): ?string { return $this->imageAltEs; }
    public function setImageAltEs(?string $v): static { $this->imageAltEs = $v; return $this; }

    public function isIsHighlighted(): bool { return $this->isHighlighted; }
    public function setIsHighlighted(bool $v): static { $this->isHighlighted = $v; return $this; }

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }

    /** @return Collection<int, NewsImage> */
    public function getImages(): Collection { return $this->images; }

    public function addImage(NewsImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setNews($this);
        }
        return $this;
    }

    public function removeImage(NewsImage $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getNews() === $this) {
                $image->setNews(null);
            }
        }
        return $this;
    }
}
