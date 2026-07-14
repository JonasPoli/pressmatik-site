<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactMessageRepository::class)]
class ContactMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cpfCnpj = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $productInterest = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    /** 'contact' or 'quote' */
    #[ORM\Column(length: 20)]
    private ?string $type = 'contact';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $productSlug = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private bool $isRead = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // ─── Getters/Setters ────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(?string $v): static { $this->name = $v; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $v): static { $this->email = $v; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $v): static { $this->phone = $v; return $this; }

    public function getCpfCnpj(): ?string { return $this->cpfCnpj; }
    public function setCpfCnpj(?string $v): static { $this->cpfCnpj = $v; return $this; }

    public function getCompany(): ?string { return $this->company; }
    public function setCompany(?string $v): static { $this->company = $v; return $this; }

    public function getProductInterest(): ?string { return $this->productInterest; }
    public function setProductInterest(?string $v): static { $this->productInterest = $v; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(?string $v): static { $this->message = $v; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(?string $v): static { $this->type = $v; return $this; }

    public function getProductSlug(): ?string { return $this->productSlug; }
    public function setProductSlug(?string $v): static { $this->productSlug = $v; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $v): static { $this->createdAt = $v; return $this; }

    public function isIsRead(): bool { return $this->isRead; }
    public function setIsRead(bool $v): static { $this->isRead = $v; return $this; }
}
