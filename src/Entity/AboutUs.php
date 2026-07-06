<?php

namespace App\Entity;

use App\Repository\AboutUsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: AboutUsRepository::class)]
#[Vich\Uploadable]
class AboutUs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ─── Título ─────────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEs = null;

    // ─── Subtítulo ──────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subtitleEs = null;

    // ─── Descrição ──────────────────────────────────────────────────────────
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEs = null;

    // ─── Missão ─────────────────────────────────────────────────────────────
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $missionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $missionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $missionEs = null;

    // ─── Visão ──────────────────────────────────────────────────────────────
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $visionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $visionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $visionEs = null;

    // ─── Valores ────────────────────────────────────────────────────────────
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $valuesPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $valuesEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $valuesEs = null;

    // ─── Vantagem 1 ─────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage1TitlePt = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage1TitleEn = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage1TitleEs = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage1DescPt = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage1DescEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage1DescEs = null;
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $advantage1Icon = null;

    // ─── Vantagem 2 ─────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage2TitlePt = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage2TitleEn = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage2TitleEs = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage2DescPt = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage2DescEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage2DescEs = null;
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $advantage2Icon = null;

    // ─── Vantagem 3 ─────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage3TitlePt = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage3TitleEn = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage3TitleEs = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage3DescPt = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage3DescEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage3DescEs = null;
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $advantage3Icon = null;

    // ─── Vantagem 4 ─────────────────────────────────────────────────────────
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage4TitlePt = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage4TitleEn = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $advantage4TitleEs = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage4DescPt = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage4DescEn = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $advantage4DescEs = null;
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $advantage4Icon = null;

    // ─── Banner Image ───────────────────────────────────────────────────────
    #[Vich\UploadableField(mapping: 'about', fileNameProperty: 'bannerImageName')]
    private ?File $bannerImageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $bannerImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // ─── Gallery Images (OneToMany) ─────────────────────────────────────────
    /** @var Collection<int, AboutGalleryImage> */
    #[ORM\OneToMany(targetEntity: AboutGalleryImage::class, mappedBy: 'aboutUs', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $galleryImages;

    public function __construct()
    {
        $this->galleryImages = new ArrayCollection();
    }

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getTitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->titleEn,
            'es' => $this->titleEs,
            default => $this->titlePt,
        };
    }

    public function getSubtitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->subtitleEn,
            'es' => $this->subtitleEs,
            default => $this->subtitlePt,
        };
    }

    public function getDescription(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->descriptionEn,
            'es' => $this->descriptionEs,
            default => $this->descriptionPt,
        };
    }

    public function getMission(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->missionEn,
            'es' => $this->missionEs,
            default => $this->missionPt,
        };
    }

    public function getVision(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->visionEn,
            'es' => $this->visionEs,
            default => $this->visionPt,
        };
    }

    public function getValues(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->valuesEn,
            'es' => $this->valuesEs,
            default => $this->valuesPt,
        };
    }

    public function getAdvantageTitle(int $num, string $locale): ?string
    {
        $prop = "advantage{$num}Title" . ucfirst($locale === 'pt' ? 'Pt' : ($locale === 'en' ? 'En' : 'Es'));
        return $this->$prop ?? null;
    }

    public function getAdvantageDesc(int $num, string $locale): ?string
    {
        $prop = "advantage{$num}Desc" . ucfirst($locale === 'pt' ? 'Pt' : ($locale === 'en' ? 'En' : 'Es'));
        return $this->$prop ?? null;
    }

    public function getAdvantageIcon(int $num): ?string
    {
        $prop = "advantage{$num}Icon";
        return $this->$prop ?? null;
    }

    // ─── Standard Getters/Setters ───────────────────────────────────────────

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

    public function getDescriptionPt(): ?string { return $this->descriptionPt; }
    public function setDescriptionPt(?string $v): static { $this->descriptionPt = $v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $v): static { $this->descriptionEn = $v; return $this; }
    public function getDescriptionEs(): ?string { return $this->descriptionEs; }
    public function setDescriptionEs(?string $v): static { $this->descriptionEs = $v; return $this; }

    public function getMissionPt(): ?string { return $this->missionPt; }
    public function setMissionPt(?string $v): static { $this->missionPt = $v; return $this; }
    public function getMissionEn(): ?string { return $this->missionEn; }
    public function setMissionEn(?string $v): static { $this->missionEn = $v; return $this; }
    public function getMissionEs(): ?string { return $this->missionEs; }
    public function setMissionEs(?string $v): static { $this->missionEs = $v; return $this; }

    public function getVisionPt(): ?string { return $this->visionPt; }
    public function setVisionPt(?string $v): static { $this->visionPt = $v; return $this; }
    public function getVisionEn(): ?string { return $this->visionEn; }
    public function setVisionEn(?string $v): static { $this->visionEn = $v; return $this; }
    public function getVisionEs(): ?string { return $this->visionEs; }
    public function setVisionEs(?string $v): static { $this->visionEs = $v; return $this; }

    public function getValuesPt(): ?string { return $this->valuesPt; }
    public function setValuesPt(?string $v): static { $this->valuesPt = $v; return $this; }
    public function getValuesEn(): ?string { return $this->valuesEn; }
    public function setValuesEn(?string $v): static { $this->valuesEn = $v; return $this; }
    public function getValuesEs(): ?string { return $this->valuesEs; }
    public function setValuesEs(?string $v): static { $this->valuesEs = $v; return $this; }

    public function getAdvantage1TitlePt(): ?string { return $this->advantage1TitlePt; }
    public function setAdvantage1TitlePt(?string $v): static { $this->advantage1TitlePt = $v; return $this; }
    public function getAdvantage1TitleEn(): ?string { return $this->advantage1TitleEn; }
    public function setAdvantage1TitleEn(?string $v): static { $this->advantage1TitleEn = $v; return $this; }
    public function getAdvantage1TitleEs(): ?string { return $this->advantage1TitleEs; }
    public function setAdvantage1TitleEs(?string $v): static { $this->advantage1TitleEs = $v; return $this; }
    public function getAdvantage1DescPt(): ?string { return $this->advantage1DescPt; }
    public function setAdvantage1DescPt(?string $v): static { $this->advantage1DescPt = $v; return $this; }
    public function getAdvantage1DescEn(): ?string { return $this->advantage1DescEn; }
    public function setAdvantage1DescEn(?string $v): static { $this->advantage1DescEn = $v; return $this; }
    public function getAdvantage1DescEs(): ?string { return $this->advantage1DescEs; }
    public function setAdvantage1DescEs(?string $v): static { $this->advantage1DescEs = $v; return $this; }
    public function getAdvantage1Icon(): ?string { return $this->advantage1Icon; }
    public function setAdvantage1Icon(?string $v): static { $this->advantage1Icon = $v; return $this; }

    public function getAdvantage2TitlePt(): ?string { return $this->advantage2TitlePt; }
    public function setAdvantage2TitlePt(?string $v): static { $this->advantage2TitlePt = $v; return $this; }
    public function getAdvantage2TitleEn(): ?string { return $this->advantage2TitleEn; }
    public function setAdvantage2TitleEn(?string $v): static { $this->advantage2TitleEn = $v; return $this; }
    public function getAdvantage2TitleEs(): ?string { return $this->advantage2TitleEs; }
    public function setAdvantage2TitleEs(?string $v): static { $this->advantage2TitleEs = $v; return $this; }
    public function getAdvantage2DescPt(): ?string { return $this->advantage2DescPt; }
    public function setAdvantage2DescPt(?string $v): static { $this->advantage2DescPt = $v; return $this; }
    public function getAdvantage2DescEn(): ?string { return $this->advantage2DescEn; }
    public function setAdvantage2DescEn(?string $v): static { $this->advantage2DescEn = $v; return $this; }
    public function getAdvantage2DescEs(): ?string { return $this->advantage2DescEs; }
    public function setAdvantage2DescEs(?string $v): static { $this->advantage2DescEs = $v; return $this; }
    public function getAdvantage2Icon(): ?string { return $this->advantage2Icon; }
    public function setAdvantage2Icon(?string $v): static { $this->advantage2Icon = $v; return $this; }

    public function getAdvantage3TitlePt(): ?string { return $this->advantage3TitlePt; }
    public function setAdvantage3TitlePt(?string $v): static { $this->advantage3TitlePt = $v; return $this; }
    public function getAdvantage3TitleEn(): ?string { return $this->advantage3TitleEn; }
    public function setAdvantage3TitleEn(?string $v): static { $this->advantage3TitleEn = $v; return $this; }
    public function getAdvantage3TitleEs(): ?string { return $this->advantage3TitleEs; }
    public function setAdvantage3TitleEs(?string $v): static { $this->advantage3TitleEs = $v; return $this; }
    public function getAdvantage3DescPt(): ?string { return $this->advantage3DescPt; }
    public function setAdvantage3DescPt(?string $v): static { $this->advantage3DescPt = $v; return $this; }
    public function getAdvantage3DescEn(): ?string { return $this->advantage3DescEn; }
    public function setAdvantage3DescEn(?string $v): static { $this->advantage3DescEn = $v; return $this; }
    public function getAdvantage3DescEs(): ?string { return $this->advantage3DescEs; }
    public function setAdvantage3DescEs(?string $v): static { $this->advantage3DescEs = $v; return $this; }
    public function getAdvantage3Icon(): ?string { return $this->advantage3Icon; }
    public function setAdvantage3Icon(?string $v): static { $this->advantage3Icon = $v; return $this; }

    public function getAdvantage4TitlePt(): ?string { return $this->advantage4TitlePt; }
    public function setAdvantage4TitlePt(?string $v): static { $this->advantage4TitlePt = $v; return $this; }
    public function getAdvantage4TitleEn(): ?string { return $this->advantage4TitleEn; }
    public function setAdvantage4TitleEn(?string $v): static { $this->advantage4TitleEn = $v; return $this; }
    public function getAdvantage4TitleEs(): ?string { return $this->advantage4TitleEs; }
    public function setAdvantage4TitleEs(?string $v): static { $this->advantage4TitleEs = $v; return $this; }
    public function getAdvantage4DescPt(): ?string { return $this->advantage4DescPt; }
    public function setAdvantage4DescPt(?string $v): static { $this->advantage4DescPt = $v; return $this; }
    public function getAdvantage4DescEn(): ?string { return $this->advantage4DescEn; }
    public function setAdvantage4DescEn(?string $v): static { $this->advantage4DescEn = $v; return $this; }
    public function getAdvantage4DescEs(): ?string { return $this->advantage4DescEs; }
    public function setAdvantage4DescEs(?string $v): static { $this->advantage4DescEs = $v; return $this; }
    public function getAdvantage4Icon(): ?string { return $this->advantage4Icon; }
    public function setAdvantage4Icon(?string $v): static { $this->advantage4Icon = $v; return $this; }

    // ─── Banner Image ───────────────────────────────────────────────────────

    public function setBannerImageFile(?File $file = null): void
    {
        $this->bannerImageFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getBannerImageFile(): ?File { return $this->bannerImageFile; }
    public function setBannerImageName(?string $v): void { $this->bannerImageName = $v; }
    public function getBannerImageName(): ?string { return $this->bannerImageName; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    // ─── Gallery Images ─────────────────────────────────────────────────────

    /** @return Collection<int, AboutGalleryImage> */
    public function getGalleryImages(): Collection { return $this->galleryImages; }

    public function addGalleryImage(AboutGalleryImage $image): static
    {
        if (!$this->galleryImages->contains($image)) {
            $this->galleryImages->add($image);
            $image->setAboutUs($this);
        }
        return $this;
    }

    public function removeGalleryImage(AboutGalleryImage $image): static
    {
        if ($this->galleryImages->removeElement($image)) {
            if ($image->getAboutUs() === $this) {
                $image->setAboutUs(null);
            }
        }
        return $this;
    }
}
