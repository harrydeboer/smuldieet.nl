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
        ORM\OneToMany(targetEntity: "App\Entity\DayFoodstuffWeight", mappedBy: "day", cascade: ["persist", "remove"]),
        Assert\Valid
    ]
    private Collection $foodstuffWeights;

    #[
        ORM\OneToMany(targetEntity: "App\Entity\DayRecipeWeight", mappedBy: "day", cascade: ["persist", "remove"]),
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
        foreach ($foodstuffWeights as $foodstuffWeight) {
            $foodstuffWeight->setDay($this);
        }
        $this->foodstuffWeights = $foodstuffWeights;
    }

    public function addFoodstuffWeight(DayFoodstuffWeight $foodstuffWeight): void
    {
        $foodstuffWeight->setDay($this);
        $this->foodstuffWeights->add($foodstuffWeight);
    }

    public function removeFoodstuffWeight(DayFoodstuffWeight $foodstuffWeight): void
    {
        $this->foodstuffWeights->removeElement($foodstuffWeight);
    }

    public function getRecipeWeights(): Collection
    {
        return $this->recipeWeights;
    }

    public function setRecipeWeights(Collection $recipeWeights): void
    {
        foreach ($recipeWeights as $recipeWeight) {
            $recipeWeight->setDay($this);
        }
        $this->recipeWeights = $recipeWeights;
    }

    public function addRecipeWeight(DayRecipeWeight $recipeWeight): void
    {
        $recipeWeight->setDay($this);
        $this->recipeWeights->add($recipeWeight);
    }

    public function removeRecipeWeight(DayRecipeWeight $recipeWeight): void
    {
        $this->recipeWeights->removeElement($recipeWeight);
    }
}
