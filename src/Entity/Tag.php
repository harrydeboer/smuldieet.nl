<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: TagRepository::class),
    ORM\Table(name: "tag"),
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
        ORM\Column(type: "string"),
        Assert\Length(min: 1, max: 255, minMessage: 'De naam mag niet leeg zijn.',
            maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-zÀ-ÿ0-9_\s\-'\",\.\*&^%$#!@:;\\/<>{}\[\]|?`\+~=\(\)]+$/",
            message: "Toegestane tekens zijn letters, cijfers en { _-'\",.*&^%$#!@:;/<>{}[]?`\\+~=()}."),
    ]
    private string $name;

    #[
        ORM\ManyToMany(targetEntity: "Recipe", inversedBy: "tags"),
        ORM\JoinTable(name: "tag_recipe"),
        ORM\JoinColumn(name: "tag_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $recipes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getRecipes(): ArrayCollection|Collection
    {
        return $this->recipes;
    }

    public function setRecipes(ArrayCollection|Collection $recipes): void
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
