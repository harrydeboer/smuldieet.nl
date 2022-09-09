<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DayRepository;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[
    ORM\Entity(repositoryClass: DayRepository::class),
    ORM\Table(name: "day"),
    ORM\UniqueConstraint(name: "timestamp_unique", columns: ["user_id", "timestamp"]),
    UniqueEntity(fields: ["user", "timestamp"], message: "Er is al een dag met deze datum."),
]
class Day implements FoodstuffsInterface, RecipesInterface
{
    public static array $recipeChoicesArray = [
        '¼' => 0.25,
        '½' => 0.5,
        '¾' => 0.75,
        '1' => 1,
        '1½' => 1.5,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
    ];

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
        ORM\ManyToMany(targetEntity: "Foodstuff", inversedBy: "days", indexBy: "id"),
        ORM\JoinTable(name: "day_foodstuff"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "foodstuff_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $foodstuffs;

    #[
        ORM\ManyToMany(targetEntity: "Recipe", inversedBy: "days", indexBy: "id"),
        ORM\JoinTable(name: "day_recipe"),
        ORM\JoinColumn(name: "day_id", referencedColumnName: "id", onDelete: "CASCADE"),
        ORM\InverseJoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE"),
    ]
    private Collection $recipes;

    #[ORM\Column(type: "string")]
    private string $recipeChoices = 'a:0:{}';

    #[ORM\Column(type: "string")]
    protected string $foodstuffWeights = 'a:0:{}';

    #[ORM\Column(type: "string")]
    protected string $foodstuffChoices = 'a:0:{}';

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

    public function getDate(): ?DateTime
    {
        if (!is_null($this->timestamp)) {
            $date = new DateTime();
            $date->setTimestamp($this->timestamp);

            return $date;
        }

        return null;
    }

    public function setDate(?DateTime $date): void
    {
        if (is_null($date)) {
            $this->timestamp = null;
        } else {
            $this->timestamp = $date->getTimestamp();
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

        $this->foodstuffs->set($foodstuff->getId(), $foodstuff);
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

        $this->recipes->set($recipe->getId(), $recipe);
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

    public function getRecipeChoices(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->recipeChoices) as $key => $value) {
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setRecipeChoices(ArrayCollection $collection): void
    {
        $this->recipeChoices = serialize($collection->toArray());
    }

    public function getFoodstuffWeights(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->foodstuffWeights) as $key => $value) {
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setFoodstuffWeights(ArrayCollection $collection): void
    {
        $this->foodstuffWeights = serialize($collection->toArray());
    }

    public function getFoodstuffChoices(): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach (unserialize($this->foodstuffChoices) as $key => $value) {
            $collection->set($key, $value);
        }

        return $collection;
    }

    public function setFoodstuffChoices(ArrayCollection $collection): void
    {
        $this->foodstuffChoices = serialize($collection->toArray());
    }
}
