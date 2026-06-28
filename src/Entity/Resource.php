<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\ResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
/**
 * Class Resource.
 */
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    private Collection $tags;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Get the ID.
     *
     * @return int|null identyfikator zasobu.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title.
     *
     * @return string|null tytul zasobu.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param string $title tytul zasobu.
     *
     * @return $this
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the author.
     *
     * @return string|null autor zasobu.
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set the author.
     *
     * @param string $author autor zasobu.
     *
     * @return $this
     */
    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the type.
     *
     * @return string|null typ zasobu.
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @param string $type typ zasobu.
     *
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the quantity.
     *
     * @return int|null dostepna ilosc sztuk.
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity.
     *
     * @param int $quantity dostepna ilosc sztuk.
     *
     * @return $this
     */
    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the category.
     *
     * @return Category|null kategoria zasobu.
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set the category.
     *
     * @param Category|null $category kategoria zasobu.
     *
     * @return $this
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add a tag.
     *
     * @param Tag $tag tag do dodania.
     *
     * @return $this
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Remove a tag.
     *
     * @param Tag $tag tag do usuniecia.
     *
     * @return $this
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Convert to string.
     *
     * @return string tytul zasobu jako tekst.
     */
    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
