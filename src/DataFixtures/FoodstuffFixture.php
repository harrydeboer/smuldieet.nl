<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Foodstuff;
use App\Entity\Nutrient;
use App\Repository\NutrientRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FoodstuffFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setName('test');
        $foodstuff->setUser($this->userRepository->findOneBy(['username' => 'test']));
        $this->dress($foodstuff);
        $manager->persist($foodstuff);

        $foodstuff = new Foodstuff();
        $foodstuff->setName('verified');
        $foodstuff->setUser($this->userRepository->findOneBy(['username' => 'testVerified']));
        $this->dress($foodstuff);
        $manager->persist($foodstuff);

        $foodstuff = new Foodstuff();
        $foodstuff->setName('anonymous');
        $this->dress($foodstuff);
        $manager->persist($foodstuff);

        $manager->flush();
    }

    private function dress(Foodstuff $foodstuff): void
    {
        $foodstuff->setCreatedAt(time());
        foreach ($this->nutrientRepository->findAll() as $nutrient) {
            $foodstuff->{'set' . ucfirst($nutrient->getName())}($this->randomNutritionalValue() / 1000);
        }
        $foodstuff->setEnergy($this->randomNutritionalValue());
        $foodstuff->setCarbohydrates($this->randomNutritionalValue());
        $foodstuff->setProtein(rand(1,20));
        $foodstuff->setFat($this->randomNutritionalValue());
        $foodstuff->setAlcohol($this->randomNutritionalValue());
        $foodstuff->setDietaryFiber($this->randomNutritionalValue());
        $foodstuff->setSalt($this->randomNutritionalValue());
        $foodstuff->setSucre($foodstuff->getCarbohydrates());
        $energy = $foodstuff->getCarbohydrates() * 4
            + $foodstuff->getProtein() * 4
            + $foodstuff->getFat() * 9
            + $foodstuff->getAlcohol() * 7
            + $foodstuff->getDietaryFiber() * 2;
        $foodstuff->setEnergy($energy);
        $weight = $foodstuff->getFat()
            + $foodstuff->getCarbohydrates()
            + $foodstuff->getProtein()
            + $foodstuff->getDietaryFiber()
            + $foodstuff->getSalt();
        $foodstuff->setWater(100 - $weight);

        $foodstuff->setPieceWeight($this->randomNutritionalValue());
        $foodstuff->setLiquid(rand(0, 1) === 1);

        if (is_null($foodstuff->getPieceWeight()) && rand(0, 1) === 1) {
            if ($foodstuff->isLiquid()) {
                $unit = array_rand(array_merge(Nutrient::SOLID_UNITS, Nutrient::LIQUID_UNITS));
            } else {
                $unit = array_rand(Nutrient::SOLID_UNITS);
            }
            $foodstuff->setPieceName($unit);
            $foodstuff->setPiecesName($unit);
        } elseif (!is_null($foodstuff->getPieceWeight()) && rand(0, 1) === 1) {
            $foodstuff->setPieceName(uniqid('test'));
            $foodstuff->setPiecesName(uniqid('tests'));
        }
        if ($foodstuff->isLiquid() && rand(0, 1) === 1) {
            $foodstuff->setDensity(rand(1, 200) / 100);
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            NutrientFixture::class,
        ];
    }

    private function randomNutritionalValue(): ?float
    {
        if (rand(0, 1) === 1) {
            return rand(1, 10);
        } else {
            return null;
        }
    }
}
