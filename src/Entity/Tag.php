<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
/**
 * Class Tag.
 */
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * Get the ID.
     * @return int|null opis wartosci zwracanej.     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name.
     * @return string|null opis wartosci zwracanej.     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name opis parametru.     *
     * @return $this opis wartosci zwracanej.     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Convert to string.
     * @return string opis wartosci zwracanej.     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
