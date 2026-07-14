<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Vich\Uploadable]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, unique: true)]
    private ?string $slug = null;

    /** Category: hydraulic, servo-hydraulic, mechanical, equipments, parts */
    #[ORM\Column(length: 50)]
    private ?string $category = null;

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
    private ?string $tonnage = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private bool $hasSpecs = true;

    /** @var Collection<int, Subproduct> */
    #[ORM\OneToMany(targetEntity: Subproduct::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $subproducts;

    #[ORM\ManyToOne(targetEntity: Subproduct::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Subproduct $defaultSubproduct = null;

    /** @var Collection<int, ProductSize> */
    #[ORM\OneToMany(targetEntity: ProductSize::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $sizes;

    /** @var Collection<int, ProductConfigItem> */
    #[ORM\OneToMany(targetEntity: ProductConfigItem::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $configItems;

    /** @var Collection<int, ProductVideo> */
    #[ORM\OneToMany(targetEntity: ProductVideo::class, mappedBy: 'product', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $videos;

    public function __construct()
    {
        $this->subproducts = new ArrayCollection();
        $this->sizes = new ArrayCollection();
        $this->configItems = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

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
        return $this->namePt ?? $this->slug ?? '';
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(?string $v): static { $this->slug = $v; return $this; }

    public function getCategory(): ?string { return $this->category; }
    public function setCategory(?string $v): static { $this->category = $v; return $this; }

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

    public function getTonnage(): ?string { return $this->tonnage; }
    public function setTonnage(?string $v): static { $this->tonnage = $v; return $this; }

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

    public function isHasSpecs(): bool { return $this->hasSpecs; }
    public function setHasSpecs(bool $v): static { $this->hasSpecs = $v; return $this; }

    /** @return Collection<int, Subproduct> */
    public function getSubproducts(): Collection { return $this->subproducts; }
    public function addSubproduct(Subproduct $s): static
    {
        if (!$this->subproducts->contains($s)) {
            $this->subproducts->add($s);
            $s->setProduct($this);
        }
        return $this;
    }
    public function removeSubproduct(Subproduct $s): static
    {
        if ($this->subproducts->removeElement($s)) {
            if ($s->getProduct() === $this) {
                $s->setProduct(null);
            }
        }
        return $this;
    }

    public function getDefaultSubproduct(): ?Subproduct { return $this->defaultSubproduct; }
    public function setDefaultSubproduct(?Subproduct $v): static { $this->defaultSubproduct = $v; return $this; }

    /** @return Collection<int, ProductSize> */
    public function getSizes(): Collection { return $this->sizes; }

    /** @return Collection<int, ProductConfigItem> */
    public function getConfigItems(): Collection { return $this->configItems; }

    /** @return Collection<int, ProductVideo> */
    public function getVideos(): Collection { return $this->videos; }
}
