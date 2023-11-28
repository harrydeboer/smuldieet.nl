<?php

declare(strict_types=1);

namespace App\Entity;

interface DietInterface
{
    public const array CHOICES = [
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

    public function isVegetarian(): bool;

    public function setVegetarian(bool $vegetarian): void;

    public function isVegan(): bool;

    public function setVegan(bool $vegan): void;

    public function isHistamineFree(): bool;

    public function setHistamineFree(bool $histamineFree): void;

    public function isCowMilkFree(): bool;

    public function setCowMilkFree(bool $cowMilkFree): void;

    public function isSoyFree(): bool;

    public function setSoyFree(bool $soyFree): void;

    public function isGlutenFree(): bool;

    public function setGlutenFree(bool $glutenFree): void;

    public function isChickenEggProteinFree(): bool;

    public function setChickenEggProteinFree(bool $chickenEggProteinFree): void;

    public function isNutFree(): bool;

    public function setNutFree(bool $nutFree): void;

    public function isWithoutPackagesAndBags(): bool;

    public function setWithoutPackagesAndBags(bool $withoutPackagesAndBags): void;
}
