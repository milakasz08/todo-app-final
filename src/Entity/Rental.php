<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]

/**
 * Class Rental.
 */
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $borrowerName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $rentedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $returnedAt = null;

    #[ORM\ManyToOne(targetEntity: Resource::class)]
    private ?Resource $resource = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(length: 20, options: ['default' => 'PENDING'])]
    private ?string $status = 'PENDING';

    /**
     * Get the ID.
     *
     * @return int|null identyfikator wypozyczenia     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the borrower name.
     *
     * @return string|null nazwa lub email wypozyczajacego     */
    public function getBorrowerName(): ?string
    {
        return $this->borrowerName;
    }

    /**
     * Set the borrower name.
     *
     * @param string $borrowerName nazwa lub email wypozyczajacego     *
     *
     * @return $this
     */
    public function setBorrowerName(string $borrowerName): static
    {
        $this->borrowerName = $borrowerName;

        return $this;
    }

    /**
     * Get the rented at date.
     *
     * @return \DateTimeImmutable|null data zlozenia wniosku     */
    public function getRentedAt(): ?\DateTimeImmutable
    {
        return $this->rentedAt;
    }

    /**
     * Set the rented at date.
     *
     * @param \DateTimeImmutable $rentedAt data zlozenia wniosku     *
     *
     * @return $this
     */
    public function setRentedAt(\DateTimeImmutable $rentedAt): static
    {
        $this->rentedAt = $rentedAt;

        return $this;
    }

    /**
     * Get the returned at date.
     *
     * @return \DateTime|null data zwrotu zasobu     */
    public function getReturnedAt(): ?\DateTime
    {
        return $this->returnedAt;
    }

    /**
     * Set the returned at date.
     *
     * @param \DateTime|null $returnedAt data zwrotu zasobu     *
     *
     * @return $this
     */
    public function setReturnedAt(?\DateTime $returnedAt): static
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    /**
     * Get the resource.
     *
     * @return resource|null wypozyczony zasob     */
    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    /**
     * Set the resource.
     *
     * @param resource|null $resource wypozyczony zasob     *
     *
     * @return $this
     */
    public function setResource(?Resource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the quantity.
     *
     * @return int|null ilosc wypozyczonych sztuk     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity.
     *
     * @param int $quantity ilosc wypozyczonych sztuk     *
     *
     * @return $this
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the user.
     *
     * @return User|null uzytkownik skladajacy wniosek     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the user.
     *
     * @param User|null $user uzytkownik skladajacy wniosek     *
     *
     * @return $this
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the status.
     *
     * @return string|null status wypozyczenia     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the status.
     *
     * @param string $status status wypozyczenia     *
     *
     * @return $this
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
