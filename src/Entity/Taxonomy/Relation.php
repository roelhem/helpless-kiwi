<?php

namespace App\Entity\Taxonomy;

use App\Entity\Person\Person;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait Relation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Taxonomy\Taxonomy", inversedBy="relations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taxonomy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person\Person")
     */
    private $person;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Taxonomy\Relation", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Taxonomy\Relation", mappedBy="parent")
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTaxonomy(): ?Taxonomy
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(?Taxonomy $taxonomy): self
    {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getRoot(): self
    {
        if ($this->parent) {
            return $this->parent->getRoot();
        }

        return $this;
    }

    public function getTaxonomies(): array
    {
        return array_merge([$this->getTaxonomy()], array_map(function ($a) {
            return $a->getTaxonomy();
        }, $this->children->toArray()));
    }

    public function getAllTaxonomies(): array
    {
        return $this->getRoot()->getTaxonomies();
    }
}
