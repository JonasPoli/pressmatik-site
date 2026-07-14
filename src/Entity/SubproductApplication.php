<?php

namespace App\Entity;

use App\Repository\SubproductApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubproductApplicationRepository::class)]
class SubproductApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subproduct::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Subproduct $subproduct = null;

    #[ORM\Column(length: 255)]
    private ?string $namePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEs = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

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

    public function getSubproduct(): ?Subproduct { return $this->subproduct; }
    public function setSubproduct(?Subproduct $v): static { $this->subproduct = $v; return $this; }

    public function getNamePt(): ?string { return $this->namePt; }
    public function setNamePt(?string $v): static { $this->namePt = $v; return $this; }
    public function getNameEn(): ?string { return $this->nameEn; }
    public function setNameEn(?string $v): static { $this->nameEn = $v; return $this; }
    public function getNameEs(): ?string { return $this->nameEs; }
    public function setNameEs(?string $v): static { $this->nameEs = $v; return $this; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $v): static { $this->icon = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
