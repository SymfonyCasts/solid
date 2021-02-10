<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity({"email"}, message="It looks like that email is already registered")
 * @UniqueEntity({"username"}, message="Oh crazy - that username is already taken! Maybe by a Bigfoot?")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BigFootSighting", mappedBy="owner")
     */
    private $bigFootSightings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="owner")
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $agreedToTermsAt;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $confirmationToken;

    public function __construct()
    {
        $this->bigFootSightings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|BigFootSighting[]
     */
    public function getBigFootSightings(): Collection
    {
        return $this->bigFootSightings;
    }

    public function addBigFootSighting(BigFootSighting $bigFootSighting): self
    {
        if (!$this->bigFootSightings->contains($bigFootSighting)) {
            $this->bigFootSightings[] = $bigFootSighting;
            $bigFootSighting->setOwner($this);
        }

        return $this;
    }

    public function removeBigFootSighting(BigFootSighting $bigFootSighting): self
    {
        if ($this->bigFootSightings->contains($bigFootSighting)) {
            $this->bigFootSightings->removeElement($bigFootSighting);
            // set the owning side to null (unless already changed)
            if ($bigFootSighting->getOwner() === $this) {
                $bigFootSighting->setOwner(null);
            }
        }

        return $this;
    }

    public function getAvatarUrl(): string
    {
        return sprintf('https://api.adorable.io/avatars/70/%s.png', $this->getEmail());
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getAgreedToTermsAt(): ?\DateTimeInterface
    {
        return $this->agreedToTermsAt;
    }

    public function setAgreedToTermsAt(\DateTimeInterface $agreedToTermsAt): self
    {
        $this->agreedToTermsAt = $agreedToTermsAt;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }
}
