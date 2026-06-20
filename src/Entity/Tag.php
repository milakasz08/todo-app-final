<?php

/*
 * This file is part of the EPI project.
 */

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Collection<int, resource>
     */
    #[ORM\ManyToMany(targetEntity: Resource::class, mappedBy: 'tags')]
    private Collection $resources;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->resources = new ArrayCollection();
    }

    /**
     * Get the ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, resource>
     */
    public function getResources(): Collection
    {
        return $this->resources;
    }

    /**
     * Add a resource.
     *
     * @param Resource $resource
     *
     * @return $this
     */
    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->addTag($this);
        }

        return $this;
    }

    /**
     * Remove a resource.
     *
     * @param Resource $resource
     *
     * @return $this
     */
    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource)) {
            $resource->removeTag($this);
        }

        return $this;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
