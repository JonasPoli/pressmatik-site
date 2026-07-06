<?php

namespace App\Entity;

use App\Repository\TechnicalSpecificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicalSpecificationRepository::class)]
class TechnicalSpecification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEs = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unitPt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unitEn = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unitEs = null;

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

    public function getUnit(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->unitEn ?: $this->unitPt,
            'es' => $this->unitEs ?: $this->unitPt,
            default => $this->unitPt,
        };
    }

    public function __toString(): string
    {
        return $this->namePt ?? '';
    }

    public function getId(): ?int { return $this->id; }

    public function getNamePt(): ?string { return $this->namePt; }
    public function setNamePt(?string $v): static { $this->namePt = $v; return $this; }
    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }
    public function getNameEs(): ?string { return $this->nameEs; }
    public function setNameEs(?string $v): static { $this->nameEs = $v; return $this; }

    public function getUnitPt(): ?string { return $this->unitPt; }
    public function setUnitPt(?string $v): static { $this->unitPt = $v; return $this; }
    public function getUnitEn(): ?string { return $this->unitEn; }
    public function setUnitEn(?string $v): static { $this->unitEn = $v; return $this; }
    public function getUnitEs(): ?string { return $this->unitEs; }
    public function setUnitEs(?string $v): static { $this->unitEs = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
