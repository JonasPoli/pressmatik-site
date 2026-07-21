<?php

namespace App\Entity;

use App\Repository\StandardItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandardItemRepository::class)]
class StandardItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEs = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    public function getName(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->nameEn ?: $this->namePt,
            'es' => $this->nameEs ?: $this->namePt,
            default => $this->namePt,
        };
    }

    public function getId(): ?int { return $this->id; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $v): static { $this->icon = $v; return $this; }

    public function getNamePt(): ?string { return $this->namePt; }
    public function setNamePt(?string $v): static { $this->namePt = $v; return $this; }

    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }

    public function getNameEs(): ?string { return $this->nameEs; }
    public function setNameEs(?string $v): static { $this->nameEs = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }
}
