<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CookbookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
        ORM\Column(type: "bigint"),
    ]
    private int $createdAt;

    #[
        ORM\Column(type: "bigint", nullable: true),
    ]
    private ?int $updatedAt = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De titel mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De titel mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $title;

    #[
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "cookbooks"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false),
    ]
    private User $user;

    #[
        ORM\OneToMany(targetEntity: "App\Entity\CookbookRecipeWeight", mappedBy: "cookbook",
            cascade: ["persist", "remove"]),
    ]
    private Collection $recipeWeights;

    public function __construct()
    {
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = strip_tags($title);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getRecipeWeights(): Collection
    {
        return $this->recipeWeights;
    }

    public function setRecipeWeights(Collection $recipeWeights): void
    {
        foreach ($recipeWeights as $recipeWeight) {
            $recipeWeight->setCookbook($this);
        }
        $this->recipeWeights = $recipeWeights;
    }

    public function addRecipeWeight(CookbookRecipeWeight $recipeWeight): void
    {
        $recipeWeight->setCookbook($this);
        $this->recipeWeights->add($recipeWeight);
    }

    public function removeRecipeWeight(CookbookRecipeWeight $recipeWeight): void
    {
        $this->recipeWeights->removeElement($recipeWeight);
    }
}
