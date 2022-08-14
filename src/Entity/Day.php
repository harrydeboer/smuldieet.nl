<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\DateCheckService;
use App\Service\WeightsCorrectionService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DayRepository;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: DayRepository::class),
    ORM\Table(name: "day"),
    ORM\UniqueConstraint(name: "timestamp_unique", columns: ["user_id", "timestamp"]),
    UniqueEntity(fields: ["user", "timestamp"], message: "Er is al een dag met deze datum."),
]
class Day
{
    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "bigint", nullable: true),
    ]
    private ?int $timestamp = null;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "days"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\ManyToMany(targetEntity: "Foodstuff", inversedBy: "days"),
        ORM\JoinTable(name: "day_foodstuff"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "foodstuff_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $foodstuffs;

    #[
        ORM\ManyToMany(targetEntity: "Recipe", inversedBy: "days"),
        ORM\JoinTable(name: "day_recipe"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $recipes;

    #[ORM\Column(type: "string")]
    private string $foodstuffWeights = 'a:0:{}';

    #[ORM\Column(type: "string")]
    private string $recipeWeights = 'a:0:{}';

    private array $recipeIds = [];

    #[Pure] public function __construct()
    {
        $this->foodstuffs = new ArrayCollection();
        $this->recipes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getDate(): ?string
    {
        if (is_null($this->timestamp)) {
            return null;
        }

        return date('d-m-Y', $this->timestamp);
    }

    public function setDate(?string $date): void
    {
        if (is_null($date)) {
            $this->timestamp = null;
        } elseif (DateCheckService::checkDate($date)) {
            $this->timestamp = strtotime($date);
        } else {
            throw new InvalidArgumentException('Date not in right format.');
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getFoodstuffs(): Collection
    {
        return $this->foodstuffs;
    }

    public function setFoodstuffs(Collection $foodstuffs): void
    {
        $this->foodstuffs = $foodstuffs;
    }

    public function addFoodstuff(Foodstuff $foodstuff): void
    {
        if ($this->foodstuffs->contains($foodstuff)) {
            return;
        }

        $this->foodstuffs->add($foodstuff);
        $foodstuff->addDay($this);
    }

    public function removeFoodstuff(Foodstuff $foodstuff): void
    {
        if (!$this->foodstuffs->contains($foodstuff)) {
            return;
        }

        $this->foodstuffs->removeElement($foodstuff);
        $foodstuff->removeDay($this);
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
        $recipe->addDay($this);
    }

    public function removeRecipe(Recipe $recipe): void
    {
        if (!$this->recipes->contains($recipe)) {
            return;
        }

        $this->recipes->removeElement($recipe);
        $recipe->removeDay($this);
    }

    public function getFoodstuffWeights(): array
    {
        return unserialize($this->foodstuffWeights);
    }

    public function setFoodstuffWeights(array $values): void
    {
        $this->foodstuffWeights = WeightsCorrectionService::correctArray($values);
    }

    public function getRecipeWeights(): array
    {
        return unserialize($this->recipeWeights);
    }

    public function setRecipeWeights(array $values): void
    {
        $this->recipeWeights = WeightsCorrectionService::correctArray($values);
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
