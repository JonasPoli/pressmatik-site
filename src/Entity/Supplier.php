<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[Vich\Uploadable]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $websiteUrl = null;

    #[Vich\UploadableField(mapping: 'suppliers', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): static { $this->name = $v; return $this; }

    public function getWebsiteUrl(): ?string { return $this->websiteUrl; }
    public function setWebsiteUrl(?string $v): static { $this->websiteUrl = $v; return $this; }

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
}
