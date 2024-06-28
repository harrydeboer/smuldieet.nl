<?php

declare(strict_types=1);

namespace App\Entity;

interface NutrientsInterface
{
    public const array NAMES = [
        'energy',
        'water',
        'protein',
        'carbohydrates',
        'sucre',
        'fat',
        'saturatedFat',
        'monounsaturatedFat',
        'polyunsaturatedFat',
        'cholesterol',
        'dietaryFiber',
        'salt',
        'vitaminA',
        'vitaminB1',
        'vitaminB2',
        'vitaminB3',
        'vitaminB6',
        'vitaminB11',
        'vitaminB12',
        'vitaminC',
        'vitaminD',
        'vitaminE',
        'vitaminK',
        'potassium',
        'calcium',
        'phosphorus',
        'iron',
        'magnesium',
        'copper',
        'zinc',
        'selenium',
        'iodine',
        'manganese',
        'molybdenum',
        'chromium',
        'fluoride',
        'alcohol',
        'caffeine',
    ];

    public function getEnergy(): ?float;

    public function setEnergy(?float $energy): void;

    public function getWater(): ?float;

    public function setWater(?float $water): void;

    public function getProtein(): ?float;

    public function setProtein(?float $protein): void;

    public function getCarbohydrates(): ?float;

    public function setCarbohydrates(?float $carbohydrates): void;

    public function getSucre(): ?float;

    public function setSucre(?float $sucre): void;

    public function getFat(): ?float;

    public function setFat(?float $fat): void;

    public function getSaturatedFat(): ?float;

    public function setSaturatedFat(?float $saturatedFat): void;

    public function getMonounsaturatedFat(): ?float;

    public function setMonounsaturatedFat(?float $monounsaturatedFat): void;

    public function getPolyunsaturatedFat(): ?float;

    public function setPolyunsaturatedFat(?float $polyunsaturatedFat): void;

    public function getCholesterol(): ?float;

    public function setCholesterol(?float $cholesterol): void;

    public function getDietaryFiber(): ?float;

    public function setDietaryFiber(?float $dietaryFiber): void;

    public function getSalt(): ?float;

    public function setSalt(?float $salt): void;

    public function getVitaminA(): ?float;

    public function setVitaminA(?float $vitaminA): void;

    public function getVitaminB1(): ?float;

    public function setVitaminB1(?float $vitaminB1): void;

    public function getVitaminB2(): ?float;

    public function setVitaminB2(?float $vitaminB2): void;

    public function getVitaminB3(): ?float;

    public function setVitaminB3(?float $vitaminB3): void;

    public function getVitaminB6(): ?float;

    public function setVitaminB6(?float $vitaminB6): void;

    public function getVitaminB11(): ?float;

    public function setVitaminB11(?float $vitaminB11): void;

    public function getVitaminB12(): ?float;

    public function setVitaminB12(?float $vitaminB12): void;

    public function getVitaminC(): ?float;

    public function setVitaminC(?float $vitaminC): void;

    public function getVitaminD(): ?float;

    public function setVitaminD(?float $vitaminD): void;

    public function getVitaminE(): ?float;

    public function setVitaminE(?float $vitaminE): void;

    public function getVitaminK(): ?float;

    public function setVitaminK(?float $vitaminK): void;

    public function getPotassium(): ?float;

    public function setPotassium(?float $potassium): void;

    public function getCalcium(): ?float;

    public function setCalcium(?float $calcium): void;

    public function getPhosphorus(): ?float;

    public function setPhosphorus(?float $phosphorus): void;

    public function getIron(): ?float;

    public function setIron(?float $iron): void;

    public function getMagnesium(): ?float;

    public function setMagnesium(?float $magnesium): void;

    public function getCopper(): ?float;

    public function setCopper(?float $copper): void;

    public function getZinc(): ?float;

    public function setZinc(?float $zinc): void;

    public function getSelenium(): ?float;

    public function setSelenium(?float $selenium): void;

    public function getIodine(): ?float;

    public function setIodine(?float $iodine): void;

    public function getManganese(): ?float;

    public function setManganese(?float $manganese): void;

    public function getMolybdenum(): ?float;

    public function setMolybdenum(?float $molybdenum): void;

    public function getChromium(): ?float;

    public function setChromium(?float $chromium): void;

    public function getFluoride(): ?float;

    public function setFluoride(?float $fluoride): void;

    public function getAlcohol(): ?float;

    public function setAlcohol(?float $alcohol): void;

    public function getCaffeine(): ?float;

    public function setCaffeine(?float $caffeine): void;
}
