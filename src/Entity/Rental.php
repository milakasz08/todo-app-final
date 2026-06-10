<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
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

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?Resource $resource = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowerName(): ?string
    {
        return $this->borrowerName;
    }

    public function setBorrowerName(string $borrowerName): static
    {
        $this->borrowerName = $borrowerName;

        return $this;
    }

    public function getRentedAt(): ?\DateTimeImmutable
    {
        return $this->rentedAt;
    }

    public function setRentedAt(\DateTimeImmutable $rentedAt): static
    {
        $this->rentedAt = $rentedAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTime
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTime $returnedAt): static
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

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
}
