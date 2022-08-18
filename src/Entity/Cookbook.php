<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CookbookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: CookbookRepository::class),
    ORM\Table(name: "cookbook"),
    ORM\UniqueConstraint(name: "title_unique", columns: ["user_id", "title"]),
    UniqueEntity(fields: ["user", "title"], message: "Er is al een kookboek met deze titel."),
]
class Cookbook
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "string"),
        Assert\Length(min: 1, max: 255, minMessage: 'De titel mag niet leeg zijn.',
            maxMessage: 'De titel mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private string $title;

    #[
        ORM\Column(type: "bigint"),
    ]
    private int $timestamp;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "cookbooks"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\ManyToMany(targetEntity: "Recipe", inversedBy: "cookbooks"),
        ORM\JoinTable(name: "cookbook_recipe"),
        ORM\JoinColumn(name: "cookbook_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $recipes;

    private array $recipeIds = [];

    #[Pure] public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = strip_tags($title);
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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
        $recipe->addCookbook($this);
    }

    public function removeRecipe(Recipe $recipe): void
    {
        if (!$this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->removeElement($recipe);
        $recipe->removeCookbook($this);
    }

    public function getRecipeIds(): array
    {
        return $this->recipeIds;
    }

    public function setRecipeIds(array $recipeIds): void
    {
        $this->recipeIds = $recipeIds;
    }
}
