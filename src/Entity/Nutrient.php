<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NutrientRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: NutrientRepository::class),
    ORM\Table(name: "nutrient"),
    ORM\UniqueConstraint(fields: ["name"]),
    ORM\UniqueConstraint(fields: ["displayName"]),
    UniqueEntity(fields: ["name"], message: "Deze naam bestaat al."),
    UniqueEntity(fields: ["displayName"], message: "Deze weergave naam bestaat al."),
]
class Nutrient
{
    public const array ENERGY_UNITS = [
        'kcal' => 4.184,
        'kJ' => 1,
    ];

    public const array SOLID_UNITS = [
        'g' => 1,
        'kg' => 1000,
        'el' => 10,
        'tl' => 2,
    ];

    public const array LIQUID_UNITS = [
        'ml' => 1,
        'cl' => 10,
        'dl' => 100,
        'l' => 1000,
    ];

    public const array VITAMIN_MINERAL_UNITS = [
        'mg' => 0.001,
        'μg' => 0.000001,
    ];

    #[
        ORM\Id,
        ORM\Column(type: "integer"),
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $id;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
    ]
    private string $name;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De weergave naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De weergave naam mag niet meer dan 255 tekens hebben.'),
    ]
    private string $displayName;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Minimum ADH moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $minRDA = null;

    #[
        ORM\Column(type: "float", nullable: true),
        Assert\GreaterThanOrEqual(0, message: 'Maximum ADH moet groter of gelijk aan 0 zijn.'),
    ]
    private ?float $maxRDA = null;

    #[
        ORM\Column(type: "string"),
        Assert\NotBlank(message: 'De naam mag niet leeg zijn.'),
        Assert\Length(max: 255, maxMessage: 'De naam mag niet meer dan 255 tekens hebben.'),
    ]
    private string $unit;

    #[
        ORM\Column(type: "integer"),
        Assert\NotBlank(message: 'Het aantal decimalen mag niet leeg zijn.'),
        Assert\GreaterThanOrEqual(0, message: 'Het aantal decimalen moet groter of gelijk aan 0 zijn.'),
    ]
    private int $decimalPlaces;

    private float $realised = 0;

    public function getId(): int
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

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getMinRDA(): ?float
    {
        return $this->minRDA;
    }

    public function setMinRDA(?float $minRDA): void
    {
        $this->minRDA = $minRDA;
    }

    public function getMaxRDA(): ?float
    {
        return $this->maxRDA;
    }

    public function setMaxRDA(?float $maxRDA): void
    {
        $this->maxRDA = $maxRDA;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        if (!in_array($unit, array_keys(array_merge(
            self::ENERGY_UNITS,
            self::SOLID_UNITS,
            self::LIQUID_UNITS,
            self::VITAMIN_MINERAL_UNITS,
        )))) {
            throw new InvalidArgumentException("Invalid unit.");
        }
        $this->unit = $unit;
    }

    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }

    public function setDecimalPlaces(int $decimalPlaces): void
    {
        $this->decimalPlaces = $decimalPlaces;
    }

    public function getRealised(): float
    {
        return $this->realised;
    }

    public function setRealised(float $realised): void
    {
        $this->realised = $realised;
    }

    public function getNameSnake(): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->name));
    }
}
