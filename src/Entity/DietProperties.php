<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The class stores the diet properties of the recipe.
 * This way the diet properties can be synchronized with the array from the getDietChoices method.
 */
class DietProperties extends AbstractProperties
{
    #[ORM\Column(type: "boolean")]
    protected bool $vegetarian = false;

    #[ORM\Column(type: "boolean")]
    protected bool $vegan = false;

    #[ORM\Column(type: "boolean")]
    protected bool $histamineFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $cowMilkFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $soyFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $glutenFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $chickenEggProteinFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $nutFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $withoutPackagesAndBags = false;

    public function isVegetarian(): bool
    {
        return $this->vegetarian;
    }

    public function setVegetarian(bool $vegetarian): void
    {
        $this->vegetarian = $vegetarian;
    }

    public function isVegan(): bool
    {
        return $this->vegan;
    }

    public function setVegan(bool $vegan): void
    {
        $this->vegan = $vegan;
    }

    public function isHistamineFree(): bool
    {
        return $this->histamineFree;
    }

    public function setHistamineFree(bool $histamineFree): void
    {
        $this->histamineFree = $histamineFree;
    }

    public function isCowMilkFree(): bool
    {
        return $this->cowMilkFree;
    }

    public function setCowMilkFree(bool $cowMilkFree): void
    {
        $this->cowMilkFree = $cowMilkFree;
    }

    public function isSoyFree(): bool
    {
        return $this->soyFree;
    }

    public function setSoyFree(bool $soyFree): void
    {
        $this->soyFree = $soyFree;
    }

    public function isGlutenFree(): bool
    {
        return $this->glutenFree;
    }

    public function setGlutenFree(bool $glutenFree): void
    {
        $this->glutenFree = $glutenFree;
    }

    public function isChickenEggProteinFree(): bool
    {
        return $this->chickenEggProteinFree;
    }

    public function setChickenEggProteinFree(bool $chickenEggProteinFree): void
    {
        $this->chickenEggProteinFree = $chickenEggProteinFree;
    }

    public function isNutFree(): bool
    {
        return $this->nutFree;
    }

    public function setNutFree(bool $nutFree): void
    {
        $this->nutFree = $nutFree;
    }

    public function isWithoutPackagesAndBags(): bool
    {
        return $this->withoutPackagesAndBags;
    }

    public function setWithoutPackagesAndBags(bool $withoutPackagesAndBags): void
    {
        $this->withoutPackagesAndBags = $withoutPackagesAndBags;
    }

    public static function getDietChoices(string $camelOrSnake = 'camel'): array
    {
        $arrayCamel = [
            'vegetarian' => 'Vegetarisch',
            'vegan' => 'Veganistisch',
            'histamineFree' => 'Histamine vrij',
            'cowMilkFree' => 'Koemelk vrij',
            'soyFree' => 'Soja vrij',
            'glutenFree' => 'Gluten vrij',
            'chickenEggProteinFree' => 'Kippenei eiwitvrij',
            'nutFree' => 'Noten vrij',
            'withoutPackagesAndBags' => 'Zonder pakjes en zakjes',
        ];

        if ($camelOrSnake === 'snake') {
            $arraySnake = [];
            foreach ($arrayCamel as $key => $item) {
                $arraySnake[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key))] = $item;
            }

            return $arraySnake;
        } else {

            return $arrayCamel;
        }
    }
}
