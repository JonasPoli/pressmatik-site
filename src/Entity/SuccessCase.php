<?php

namespace App\Entity;

use App\Repository\SuccessCaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: SuccessCaseRepository::class)]
#[Vich\Uploadable]
class SuccessCase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titlePt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titleEs = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionPt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $clientIndustry = null;

    #[Vich\UploadableField(mapping: 'success_cases', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $position = 0;

    #[ORM\Column]
    private bool $isActive = true;

    // ─── Locale Helpers ─────────────────────────────────────────────────────

    public function getTitle(string $locale): ?string
    {
        return match ($locale) {
            'en' => $this->titleEn ?: $this->titlePt,
            'es' => $this->titleEs ?: $this->titlePt,
            default => $this->titlePt,
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

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getTitlePt(): ?string { return $this->titlePt; }
    public function setTitlePt(?string $v): static { $this->titlePt = $v; return $this; }
    public function getTitleEn(): ?string { return $this->titleEn; }
    public function setTitleEn(?string $v): static { $this->titleEn = $v; return $this; }
    public function getTitleEs(): ?string { return $this->titleEs; }
    public function setTitleEs(?string $v): static { $this->titleEs = $v; return $this; }

    public function getDescriptionPt(): ?string { return $this->descriptionPt; }
    public function setDescriptionPt(?string $v): static { $this->descriptionPt = $v; return $this; }
    public function getDescriptionEn(): ?string { return $this->descriptionEn; }
    public function setDescriptionEn(?string $v): static { $this->descriptionEn = $v; return $this; }
    public function getDescriptionEs(): ?string { return $this->descriptionEs; }
    public function setDescriptionEs(?string $v): static { $this->descriptionEs = $v; return $this; }

    public function getClientName(?string $locale = null): ?string
    {
        if (!$this->clientName || !$locale || $locale === 'pt') {
            return $this->clientName;
        }

        $map = [
            'en' => [
                'indústria automotiva' => 'Automotive Industry',
                'indústria de borracha' => 'Rubber Industry',
                'indústria de energia' => 'Energy Industry',
                'manutenção' => 'Maintenance',
                'do brasil para o mundo' => 'From Brazil to the World',
                'metal forming co.' => 'Metal Forming Co.',
            ],
            'es' => [
                'indústria automotiva' => 'Industria Automotriz',
                'indústria de borracha' => 'Industria del Caucho',
                'indústria de energia' => 'Industria de Energía',
                'manutenção' => 'Mantenimiento',
                'do brasil para o mundo' => 'De Brasil al Mundo',
                'metal forming co.' => 'Metal Forming Co.',
            ],
        ];

        $clean = mb_strtolower(trim($this->clientName));
        return $map[$locale][$clean] ?? $this->clientName;
    }
    public function setClientName(?string $v): static { $this->clientName = $v; return $this; }

    public function getClientIndustry(?string $locale = null): ?string
    {
        if (!$this->clientIndustry || !$locale || $locale === 'pt') {
            return $this->clientIndustry;
        }

        $map = [
            'en' => [
                'automotivo' => 'Automotive',
                'indústria automotiva' => 'Automotive Industry',
                'borracha' => 'Rubber',
                'metal borracha' => 'Rubber Metal',
                'metalúrgico' => 'Metallurgical',
                'manutenção' => 'Maintenance',
                'indústria de energia' => 'Energy Industry',
                'energia' => 'Energy',
                'do brasil para o mundo' => 'From Brazil to the World',
                'pão de açúcar' => 'Sugarloaf',
            ],
            'es' => [
                'automotivo' => 'Automoción',
                'indústria automotiva' => 'Industria Automotriz',
                'borracha' => 'Caucho',
                'metal borracha' => 'Metal Caucho',
                'metalúrgico' => 'Metalúrgico',
                'manutenção' => 'Mantenimiento',
                'indústria de energia' => 'Industria de Energía',
                'energia' => 'Energía',
                'do brasil para o mundo' => 'De Brasil al Mundo',
                'pão de açúcar' => 'Pan de Azúcar',
            ],
        ];

        $clean = mb_strtolower(trim($this->clientIndustry));
        return $map[$locale][$clean] ?? $this->clientIndustry;
    }
    public function setClientIndustry(?string $v): static { $this->clientIndustry = $v; return $this; }

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
