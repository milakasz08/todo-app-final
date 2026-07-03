<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
/**
 * Class Category.
 */
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'category.name.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'category.name.too_long')]
    private ?string $name = null;

    /**
     * Get the ID.
     *
     * @return int|null identyfikator kategorii     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name.
     *
     * @return string|null nazwa kategorii     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name nazwa kategorii     *
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Convert to string.
     *
     * @return string nazwa kategorii jako tekst     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
