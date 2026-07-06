<?php

namespace App\Entity;

use App\Repository\ProductConfigItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductConfigItemRepository::class)]
class ProductConfigItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $productSlug = null;

    /** 'standard' or 'optional' */
    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEs = null;

    #[ORM\Column]
    private int $position = 0;

    public function getName(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->nameEn ?: $this->namePt,
            'es' => $this->nameEs ?: $this->namePt,
            default => $this->namePt,
        };
    }

    public function getId(): ?int { return $this->id; }

    public function getProductSlug(): ?string { return $this->productSlug; }
    public function setProductSlug(?string $v): static { $this->productSlug = $v; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $v): static { $this->type = $v; return $this; }

    public function getNamePt(): ?string { return $this->namePt; }
    public function setNamePt(?string $v): static { $this->namePt = $v; return $this; }
    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }
    public function getNameEs(): ?string { return $this->nameEs; }
    public function setNameEs(?string $v): static { $this->nameEs = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
