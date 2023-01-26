<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class DietProperties
{
    #[ORM\Column(type: "boolean")]
    protected bool $isVegetarian = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isVegan = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isHistamineFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isCowMilkFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isSoyFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isGlutenFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isChickenEggProteinFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isNutFree = false;

    #[ORM\Column(type: "boolean")]
    protected bool $isWithoutPackagesAndBags = false;

    public function getIsVegetarian(): bool
    {
        return $this->isVegetarian;
    }

    public function setIsVegetarian(bool $isVegetarian): void
    {
        $this->isVegetarian = $isVegetarian;
    }

    public function getIsVegan(): bool
    {
        return $this->isVegan;
    }

    public function setIsVegan(bool $isVegan): void
    {
        $this->isVegan = $isVegan;
    }

    public function getIsHistamineFree(): bool
    {
        return $this->isHistamineFree;
    }

    public function setIsHistamineFree(bool $isHistamineFree): void
    {
        $this->isHistamineFree = $isHistamineFree;
    }

    public function getIsCowMilkFree(): bool
    {
        return $this->isCowMilkFree;
    }

    public function setIsCowMilkFree(bool $isCowMilkFree): void
    {
        $this->isCowMilkFree = $isCowMilkFree;
    }

    public function getIsSoyFree(): bool
    {
        return $this->isSoyFree;
    }

    public function setIsSoyFree(bool $isSoyFree): void
    {
        $this->isSoyFree = $isSoyFree;
    }

    public function getIsGlutenFree(): bool
    {
        return $this->isGlutenFree;
    }

    public function setIsGlutenFree(bool $isGlutenFree): void
    {
        $this->isGlutenFree = $isGlutenFree;
    }

    public function getIsChickenEggProteinFree(): bool
    {
        return $this->isChickenEggProteinFree;
    }

    public function setIsChickenEggProteinFree(bool $isChickenEggProteinFree): void
    {
        $this->isChickenEggProteinFree = $isChickenEggProteinFree;
    }

    public function getIsNutFree(): bool
    {
        return $this->isNutFree;
    }

    public function setIsNutFree(bool $isNutFree): void
    {
        $this->isNutFree = $isNutFree;
    }

    public function getIsWithoutPackagesAndBags(): bool
    {
        return $this->isWithoutPackagesAndBags;
    }

    public function setIsWithoutPackagesAndBags(bool $isWithoutPackagesAndBags): void
    {
        $this->isWithoutPackagesAndBags = $isWithoutPackagesAndBags;
    }

    public static function getDietChoices(string $camelOrSnake = 'camel'): array
    {
        $arrayCamel = [
            'isVegetarian' => 'Vegetarisch',
            'isVegan' => 'Veganistisch',
            'isHistamineFree' => 'Histamine vrij',
            'isCowMilkFree' => 'Koemelk vrij',
            'isSoyFree' => 'Soja vrij',
            'isGlutenFree' => 'Gluten vrij',
            'isChickenEggProteinFree' => 'Kippenei eiwitvrij',
            'isNutFree' => 'Noten vrij',
            'isWithoutPackagesAndBags' => 'Zonder pakjes en zakjes',
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

    protected function getNames(): array
    {
        $vars =  get_object_vars($this);
        $names = [];
        foreach ($vars as $name => $var) {
            $names[] = $name;
        }

        return $names;
    }
}
