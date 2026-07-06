<?php

namespace App\Entity;

use App\Repository\TestimonyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: TestimonyRepository::class)]
#[Vich\Uploadable]
class Testimony
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rolePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $textEs = null;

    #[Vich\UploadableField(mapping: 'testimonies', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private int $position = 0;

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getRole(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->roleEn ?: $this->rolePt,
            'es' => $this->roleEs ?: $this->rolePt,
            default => $this->rolePt,
        };
    }

    public function getText(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->textEn ?: $this->textPt,
            'es' => $this->textEs ?: $this->textPt,
            default => $this->textPt,
        };
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): static { $this->name = $v; return $this; }

    public function getCompany(): ?string { return $this->company; }
    public function setCompany(?string $v): static { $this->company = $v; return $this; }

    public function getRolePt(): ?string { return $this->rolePt; }
    public function setRolePt(?string $v): static { $this->rolePt = $v; return $this; }
    public function getRoleEn(): ?string { return $this->roleEn; }
    public function setRoleEn(?string $v): static { $this->roleEn = $v; return $this; }
    public function getRoleEs(): ?string { return $this->roleEs; }
    public function setRoleEs(?string $v): static { $this->roleEs = $v; return $this; }

    public function getTextPt(): ?string { return $this->textPt; }
    public function setTextPt(?string $v): static { $this->textPt = $v; return $this; }
    public function getTextEn(): ?string { return $this->textEn; }
    public function setTextEn(?string $v): static { $this->textEn = $v; return $this; }
    public function getTextEs(): ?string { return $this->textEs; }
    public function setTextEs(?string $v): static { $this->textEs = $v; return $this; }

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

    public function isIsActive(): bool { return $this->isActive; }
    public function setIsActive(bool $v): static { $this->isActive = $v; return $this; }

    public function getPosition(): int { return $this->position; }
    public function setPosition(int $v): static { $this->position = $v; return $this; }
}
