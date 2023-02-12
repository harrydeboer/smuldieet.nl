<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DayRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
        ORM\OneToMany(mappedBy: "day", targetEntity: "App\Entity\FoodstuffWeight", cascade: ["persist", "remove"]),
        Assert\Valid
    ]
    private Collection $foodstuffWeights;

    #[
        ORM\OneToMany(mappedBy: "day", targetEntity: "RecipeWeight", cascade: ["persist", "remove"]),
    ]
    private Collection $recipeWeights;

    public function __construct()
    {
        $this->foodstuffWeights = new ArrayCollection();
        $this->recipeWeights = new ArrayCollection();
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

    public function getFoodstuffWeights(): Collection
    {
        return $this->foodstuffWeights;
    }

    public function setFoodstuffWeights(Collection $foodstuffWeights): void
    {
        $this->foodstuffWeights = $foodstuffWeights;
    }

    public function addFoodstuffWeight(FoodstuffWeight $foodstuffWeight): void
    {
        $foodstuffWeight->setDay($this);
        $this->foodstuffWeights->add($foodstuffWeight);
    }

    public function removeFoodstuffWeight(FoodstuffWeight $foodstuffWeight): void
    {
        $this->foodstuffWeights->removeElement($foodstuffWeight);
    }

    public function getRecipeWeights(): Collection
    {
        return $this->recipeWeights;
    }

    public function setRecipeWeights(Collection $recipeWeights): void
    {
        $this->recipeWeights = $recipeWeights;
    }

    public function addRecipeWeight(RecipeWeight $recipeWeight): void
    {
        $recipeWeight->setDay($this);
        $this->recipeWeights->add($recipeWeight);
    }

    public function removeRecipeWeight(RecipeWeight $recipeWeight): void
    {
        $this->recipeWeights->removeElement($recipeWeight);
    }
}
