<?php

namespace App\Entity;

use App\Repository\ServiceHeaderRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Attribute as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ServiceHeaderRepository::class)]
#[Vich\Uploadable]
class ServiceHeader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'services', fileNameProperty: 'videoName')]
    private ?File $videoFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $videoName = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $videoUrl = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setVideoFile(?File $file = null): void
    {
        $this->videoFile = $file;
        if (null !== $file) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    public function setVideoName(?string $v): static
    {
        $this->videoName = $v;
        return $this;
    }

    public function getVideoName(): ?string
    {
        return $this->videoName;
    }

    public function setVideoUrl(?string $v): static
    {
        $this->videoUrl = $v;
        return $this;
    }

    public function getVideoUrl(): ?string
    {
        return $this->videoUrl;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $v): static
    {
        $this->updatedAt = $v;
        return $this;
    }

    public function getActiveVideoUrl(): string
    {
        if ($this->videoName) {
            return '/uploads/services/' . $this->videoName;
        }
        if ($this->videoUrl) {
            return $this->videoUrl;
        }
        return '/images/service-bg.mp4';
    }
}
