<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Nutrient;
use App\Entity\NutrientsInterface;
use Doctrine\Persistence\ObjectManager;

class NutrientFixture extends AbstractFixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (NutrientsInterface::NAMES as $name) {
            $nutrient = new Nutrient();
            $nutrient->setName($name);
            $nutrient->setDisplayName($name);
            $nutrient->setDecimalPlaces(0);
            $nutrient->setUnit('g');
            $manager->persist($nutrient);
        }

        $manager->flush();
    }
}
