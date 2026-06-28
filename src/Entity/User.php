<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
/**
 * Class User.
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * Get the ID.
     * @return int|null opis wartosci zwracanej.     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the email.
     * @return string|null opis wartosci zwracanej.     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     *
     * @param string $email opis parametru.     *
     * @return $this opis wartosci zwracanej.     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the user identifier.
     * @return string opis wartosci zwracanej.     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the roles.
     * @return array opis wartosci zwracanej.     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     *
     * @param array $roles opis parametru.     *
     * @return $this opis wartosci zwracanej.     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the password.
     * @return string|null opis wartosci zwracanej.     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     *
     * @param string $password opis parametru.     *
     * @return $this opis wartosci zwracanej.     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Serialize the object.
     * @return array opis wartosci zwracanej.     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * Erase the credentials.
     * @return void opis wartosci zwracanej.     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Check if the user is verified.
     * @return bool opis wartosci zwracanej.     */
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     *
     * @param bool $isVerified opis parametru.     *
     * @return $this opis wartosci zwracanej.     */
    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Convert to string.
     * @return string opis wartosci zwracanej.     */
    public function __toString(): string
    {
        return $this->email ?? '';
    }
}
