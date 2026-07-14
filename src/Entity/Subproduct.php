<?php

namespace App\Entity;

use App\Repository\SubproductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: SubproductRepository::class)]
#[Vich\Uploadable]
class Subproduct
{
    /** @var Collection<int, SubproductApplication> */
    #[ORM\OneToMany(targetEntity: SubproductApplication::class, mappedBy: 'subproduct', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $applications;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'subproducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    /** Model code: PMC-ST, PMCD-GT, PM4C-RP, etc. */
    #[ORM\Column(length: 50)]
    private ?string $model = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEs = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $tag = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'pdfNamePt')]
    private ?File $pdfFilePt = null;

    #[ORM\Column(nullable: true)]
    private ?string $pdfNamePt = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'pdfNameEn')]
    private ?File $pdfFileEn = null;

    #[ORM\Column(nullable: true)]
    private ?string $pdfNameEn = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'pdfNameEs')]
    private ?File $pdfFileEs = null;

    #[ORM\Column(nullable: true)]
    private ?string $pdfNameEs = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getName(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->nameEn ?: $this->namePt,
            'es' => $this->nameEs ?: $this->namePt,
            default => $this->namePt,
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

    public function __toString(): string
    {
        return $this->model . ' — ' . ($this->namePt ?? '');
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(?Product $v): static { $this->product = $v; return $this; }

    public function getModel(): ?string { return $this->model; }
    public function setModel(?string $v): static { $this->model = $v; return $this; }

    public function getNamePt(): ?string { return $this->namePt; }
    public function setNamePt(?string $v): static { $this->namePt = $v; return $this; }
    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }
    public function getNameEs(): ?string { return $this->nameEs; }
    public function setNameEs(?string $v): static { $this->nameEs = $v; return $this; }

    public function getDescriptionPt(): ?string { return $this->descriptionPt; }
    public function setDescriptionPt(?string $v): static { $this->descriptionPt = $v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $v): static { $this->descriptionEn = $v; return $this; }
    public function getDescriptionEs(): ?string { return $this->descriptionEs; }
    public function setDescriptionEs(?string $v): static { $this->descriptionEs = $v; return $this; }

    public function getTag(): ?string { return $this->tag; }
    public function setTag(?string $v): static { $this->tag = $v; return $this; }

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

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    /** @return Collection<int, SubproductApplication> */
    public function getApplications(): Collection { return $this->applications; }

    public function addApplication(SubproductApplication $a): static
    {
        if (!$this->applications->contains($a)) {
            $this->applications->add($a);
            $a->setSubproduct($this);
        }
        return $this;
    }

    public function removeApplication(SubproductApplication $a): static
    {
        if ($this->applications->removeElement($a)) {
            if ($a->getSubproduct() === $this) {
                $a->setSubproduct(null);
            }
        }
        return $this;
    }

    public function getPdfFilePt(): ?File { return $this->pdfFilePt; }
    public function setPdfFilePt(?File $file = null): void
    {
        $this->pdfFilePt = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getPdfNamePt(): ?string { return $this->pdfNamePt; }
    public function setPdfNamePt(?string $v): void { $this->pdfNamePt = $v; }

    public function getPdfFileEn(): ?File { return $this->pdfFileEn; }
    public function setPdfFileEn(?File $file = null): void
    {
        $this->pdfFileEn = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getPdfNameEn(): ?string { return $this->pdfNameEn; }
    public function setPdfNameEn(?string $v): void { $this->pdfNameEn = $v; }

    public function getPdfFileEs(): ?File { return $this->pdfFileEs; }
    public function setPdfFileEs(?File $file = null): void
    {
        $this->pdfFileEs = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getPdfNameEs(): ?string { return $this->pdfNameEs; }
    public function setPdfNameEs(?string $v): void { $this->pdfNameEs = $v; }

    public function getPdfForLocale(string $locale): ?string
    {
        // 1. Current locale
        if ($locale === 'pt' && $this->pdfNamePt) {
            return $this->pdfNamePt;
        }
        if ($locale === 'en' && $this->pdfNameEn) {
            return $this->pdfNameEn;
        }
        if ($locale === 'es' && $this->pdfNameEs) {
            return $this->pdfNameEs;
        }

        // 2. English (en)
        if ($this->pdfNameEn) {
            return $this->pdfNameEn;
        }

        // 3. Portuguese (pt)
        if ($this->pdfNamePt) {
            return $this->pdfNamePt;
        }

        // 4. Spanish (es)
        if ($this->pdfNameEs) {
            return $this->pdfNameEs;
        }

        return null;
    }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }
}
