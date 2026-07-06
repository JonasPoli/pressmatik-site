<?php

namespace App\Entity;

use App\Repository\ProductSpecValueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductSpecValueRepository::class)]
class ProductSpecValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $productSlug = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TechnicalSpecification $specification = null;

    #[ORM\ManyToOne(inversedBy: 'specValues')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductSize $productSize = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $vTypeValue = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $hTypeValue = null;

    #[ORM\Column]
    private int $position = 0;

    public function getId(): ?int { return $this->id; }

    public function getProductSlug(): ?string { return $this->productSlug; }
    public function setProductSlug(?string $v): static { $this->productSlug = $v; return $this; }

    public function getSpecification(): ?TechnicalSpecification { return $this->specification; }
    public function setSpecification(?TechnicalSpecification $v): static { $this->specification = $v; return $this; }

    public function getProductSize(): ?ProductSize { return $this->productSize; }
    public function setProductSize(?ProductSize $v): static { $this->productSize = $v; return $this; }

    public function getVTypeValue(): ?string { return $this->vTypeValue; }
    public function setVTypeValue(?string $v): static { $this->vTypeValue = $v; return $this; }

    public function getHTypeValue(): ?string { return $this->hTypeValue; }
    public function setHTypeValue(?string $v): static { $this->hTypeValue = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
