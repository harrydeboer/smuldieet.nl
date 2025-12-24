<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: TagRepository::class),
    ORM\Table(name: "tag"),
    ORM\UniqueConstraint(fields: ["name"]),
    UniqueEntity(fields: ["name"], message: "Er is al een tag met deze naam."),
]
class Tag
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "bigint"),
    ]
    private int $createdAt;

    #[
        ORM\Column(type: "bigint", nullable: true),
    ]
    private ?int $updatedAt = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $name;

    #[
        ORM\ManyToMany(targetEntity: "App\Entity\Recipe", inversedBy: "tags", cascade: ['persist']),
        ORM\JoinTable(name: "tag_recipe"),
        ORM\JoinColumn(name: "tag_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $recipes;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function setRecipes(Collection $recipes): void
    {
        $this->recipes = $recipes;
    }

    public function addRecipe(Recipe $recipe): void
    {
        if ($this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->add($recipe);
        $recipe->addTag($this);
    }

    public function removeRecipe(Recipe $recipe): void
    {
        if (!$this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->removeElement($recipe);
        $recipe->removeTag($this);
    }
}
