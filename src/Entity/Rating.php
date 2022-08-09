<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: RatingRepository::class),
    ORM\Table(name: "rating"),
]
class Rating
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
    private int $timestamp;

    #[
        ORM\Column(type: "text", nullable: true),
        Assert\Length(max: 65535, maxMessage: 'De review mag niet meer dan 65535 tekens hebben.'),
    ]
    private ?string $content = null;

    #[
        ORM\Column(type: "integer"),
        Assert\GreaterThanOrEqual(10, message: 'De rating moet groter of gelijk zijn aan 1.'),
        Assert\LessThanOrEqual(100, message: 'De rating moet kleiner of gelijk zijn aan 10.'),
    ]
    private int $rating;

    #[ORM\Column(type: "boolean")]
    private bool $pending = true;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "ratings"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\Recipe", inversedBy: "ratings"),
        ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id"),
    ]
    private Recipe $recipe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getRating(): float
    {
        return $this->rating / 10;
    }

    public function setRating(float $rating): void
    {
        $this->rating = (int) (10 * $rating);
    }

    public function getPending(): bool
    {
        return $this->pending;
    }

    public function setPending(bool $pending): void
    {
        $this->pending = $pending;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }
}
