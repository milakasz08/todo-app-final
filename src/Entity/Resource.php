<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Enum\MediaType;
use App\Repository\ResourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\NotBlank(message: 'resource.title.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'resource.title.too_long')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'resource.author.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'resource.author.too_long')]
    private ?string $author = null;

    #[ORM\Column(length: 50, enumType: MediaType::class)]
    private ?MediaType $type = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: 'resource.quantity.positive_or_zero')]
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
     * @return int|null identyfikator zasobu
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the title.
     *
     * @return string|null tytul zasobu
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title.
     *
     * @param string|null $title tytul zasobu
     *
     * @return $this
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the author.
     *
     * @return string|null autor zasobu
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Set the author.
     *
     * @param string|null $author autor zasobu
     *
     * @return $this
     */
    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the type.
     *
     * @return MediaType|null typ zasobu
     */
    public function getType(): ?MediaType
    {
        return $this->type;
    }

    /**
     * Set the type.
     *
     * @param MediaType $type typ zasobu
     *
     * @return $this
     */
    public function setType(MediaType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the quantity.
     *
     * @return int|null dostepna ilosc sztuk
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set the quantity.
     *
     * @param int $quantity dostepna ilosc sztuk
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
     * @return Category|null kategoria zasobu
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Set the category.
     *
     * @param Category|null $category kategoria zasobu
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
     * @param Tag $tag tag do dodania
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
     * @param Tag $tag tag do usuniecia
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
     * @return string tytul zasobu jako tekst
     */
    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
