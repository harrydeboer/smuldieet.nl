<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\FoodstuffRepository;

#[
    ORM\Entity(repositoryClass: FoodstuffRepository::class),
    ORM\Table(name: "foodstuff"),
    ORM\UniqueConstraint(name: "name_unique", columns: ["user_id", "name"]),
    UniqueEntity(fields: ["user", "name"], message: "Er is al een voedingsmiddel met deze naam."),
]
class Foodstuff extends NutrientProperties
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
        ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "foodstuffs"),
        ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: true),
    ]
    private ?User $user = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%&\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%&/()+<>'\"."),
    ]
    private string $name;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private ?string $pieceName = null;

    #[
        ORM\Column(type: "string", nullable: true),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
        Assert\Regex(pattern: "/^[A-Za-zÀ-ÿ0-9\s_\-,.%\/\(\)\+<>'\"]+$/",
            message: "Toegestane tekens zijn letters, cijfers, spaties en _-,.%/()+<>'\"."),
    ]
    private ?string $piecesName = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Gewicht per stuk moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $pieceWeight = null;

    #[ORM\Column(type: "boolean")]
    private bool $isLiquid = false;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Dichtheid moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $density = null;

    #[ORM\OneToMany(mappedBy: "foodstuff", targetEntity: "App\Entity\FoodstuffWeight", cascade: ["remove"])]
    private Collection $foodstuffWeights;

    public function __construct()
    {
        $this->foodstuffWeights = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = strip_tags($name);
    }

    public function getPieceName(): ?string
    {
        return $this->pieceName;
    }

    public function setPieceName(?string $pieceName): void
    {
        $this->pieceName = $pieceName;
    }

    public function getPiecesName(): ?string
    {
        return $this->piecesName;
    }

    public function setPiecesName(?string $piecesName): void
    {
        $this->piecesName = $piecesName;
    }

    public function getPieceWeight(): ?float
    {
        return $this->pieceWeight;
    }

    public function setPieceWeight(?float $pieceWeight): void
    {
        $this->pieceWeight = $pieceWeight;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getIsLiquid(): bool
    {
        return $this->isLiquid;
    }

    public function setIsLiquid(bool $isLiquid): void
    {
        $this->isLiquid = $isLiquid;
    }

    public function getDensity(): ?float
    {
        return $this->density;
    }

    public function setDensity(?float $density): void
    {
        $this->density = $density;
    }

    public function getFoodstuffWeights(): Collection
    {
        return $this->foodstuffWeights;
    }

    public function setFoodstuffWeights(Collection $foodstuffWeights): void
    {
        $this->foodstuffWeights = $foodstuffWeights;
    }

    public function getNutrientNames(): array
    {
        $nutrientProperties = new NutrientProperties();

        return $nutrientProperties->getNames();
    }
}
