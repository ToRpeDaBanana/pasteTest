<?php

namespace App\Entity;

use App\Repository\PasteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PasteRepository::class)]
class Paste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $expirationTime = null;

    #[ORM\Column(length: 255)]
    private ?string $accessLevel = null;

    #[ORM\Column(length: 100)]
    private ?string $language = null;

    #[ORM\ManyToOne(inversedBy: 'pastes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getExpirationTime(): ?\DateTimeInterface
    {
        return $this->expirationTime;
    }

    public function setExpirationTime(\DateTimeInterface $expirationTime): static
    {
        $this->expirationTime = $expirationTime;

        return $this;
    }

    public function getAccessLevel(): ?string
    {
        return $this->accessLevel;
    }

    public function setAccessLevel(string $accessLevel): static
    {
        $this->accessLevel = $accessLevel;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function getLanguage(): ?string
{
    return $this->language;
}

public function setLanguage(?string $language): self
{
    $this->language = $language;
    return $this;
}
}