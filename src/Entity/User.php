<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique:true)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    /**
     * @var Collection<int, Paste>
     */
    #[ORM\OneToMany(targetEntity: Paste::class, mappedBy: 'user')]
    private Collection $pastes;

    public function __construct()
    {
        $this->pastes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
    /**
     * @return Collection<int, Paste>
     */
    public function getPastes(): Collection
    {
        return $this->pastes;
    }

    public function addPaste(Paste $paste): static
    {
        if (!$this->pastes->contains($paste)) {
            $this->pastes->add($paste);
            $paste->setUser($this);
        }

        return $this;
    }

    public function removePaste(Paste $paste): static
    {
        if ($this->pastes->removeElement($paste)) {
            // set the owning side to null (unless already changed)
            if ($paste->getUser() === $this) {
                $paste->setUser(null);
            }
        }

        return $this;
    }
}
