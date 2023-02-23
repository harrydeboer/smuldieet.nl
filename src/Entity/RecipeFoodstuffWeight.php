<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RecipeFoodstuffWeightRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\FoodstuffWeightConstraint;

#[
    ORM\Entity(repositoryClass: RecipeFoodstuffWeightRepository::class),
    ORM\Table(name: "recipe_foodstuff_weight"),
    FoodstuffWeightConstraint,
]
class RecipeFoodstuffWeight extends FoodstuffWeight
{
    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "foodstuffWeights"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", nullable: false),
    ]
    protected Recipe $recipe;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Foodstuff", inversedBy: "recipeFoodstuffWeights"),
        ORM\JoinColumn(name: "foodstuff_id", referencedColumnName: "id", nullable: false),
    ]
    protected Foodstuff $foodstuff;

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }

    public function getFoodstuff(): Foodstuff
    {
        return $this->foodstuff;
    }

    public function setFoodstuff(Foodstuff $foodstuff): void
    {
        $this->foodstuff = $foodstuff;
    }

    public function getFoodstuffId(): int
    {
        return $this->foodstuffId;
    }

    public function setFoodstuffId(int $foodstuffId): void
    {
        $this->foodstuffId = $foodstuffId;
    }
}
