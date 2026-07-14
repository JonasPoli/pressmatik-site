<?php

namespace App\Entity;

use App\Repository\ProductSizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductSizeRepository::class)]
class ProductSize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Subproduct::class, inversedBy: 'sizes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Subproduct $subproduct = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $hasVType = true;

    #[ORM\Column]
    private bool $hasHType = true;

    /** @var Collection<int, ProductSpecValue> */
    #[ORM\OneToMany(targetEntity: ProductSpecValue::class, mappedBy: 'productSize', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $specValues;

    public function __construct()
    {
        $this->specValues = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int { return $this->id; }

    public function getSubproduct(): ?Subproduct { return $this->subproduct; }
    public function setSubproduct(?Subproduct $v): static { $this->subproduct = $v; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): static { $this->name = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }

    public function isHasVType(): bool { return $this->hasVType; }
    public function setHasVType(bool $v): static { $this->hasVType = $v; return $this; }

    public function isHasHType(): bool { return $this->hasHType; }
    public function setHasHType(bool $v): static { $this->hasHType = $v; return $this; }

    /** @return Collection<int, ProductSpecValue> */
    public function getSpecValues(): Collection { return $this->specValues; }
}
