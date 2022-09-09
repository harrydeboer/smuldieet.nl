<?php

declare(strict_types=1);

namespace App\ValueObject;

class Nutrient
{
    private string $name;

    private string $nameNL;

    private ?float $minRDA = null;

    private ?float $maxRDA = null;

    private string $unit;

    private int $decimalPlaces;

    private float $realised = 0;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNameNL(): string
    {
        return $this->nameNL;
    }

    public function setNameNL(string $nameNL): void
    {
        $this->nameNL = $nameNL;
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
}
