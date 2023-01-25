<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;
use InvalidArgumentException;
use App\Entity\User;

/**
 * The submitted foodstuffs are combined to one foodstuff with averaged values.
 */
readonly class CombineFoodstuffsService
{
    public function __construct(
        private FoodstuffRepositoryInterface $foodstuffRepository,
        private NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    public function combine(User $user, array $formData): Foodstuff
    {
        $foodstuff = new Foodstuff();
        $foodstuff->setName($formData['name']);
        $foodstuff->setUser($user);

        $totalWeight = 0;
        foreach ($formData['foodstuff_weights'] as $weight) {
            $totalWeight += $weight->getValue();
        }

        if ((int) round(($totalWeight * 100)) !== 10000) {
            throw new InvalidArgumentException('Weights must add up to 100 percent.');
        }

        foreach ($formData['foodstuff_weights'] as $weight) {
            $foodstuffForm = $this->foodstuffRepository
                ->getDefaultAndFromUser($weight->getFoodstuffId(), $user->getId());
            foreach ($foodstuff->getNutrientNames() as $name) {
                if (is_null($foodstuffForm->{'get' . ucfirst($name)}())) {
                    continue;
                } else {
                    $foodstuff->{'set' . ucfirst($name)}($foodstuff->{'get' . ucfirst($name)}() +
                        $foodstuffForm->{'get' . ucfirst($name)}()
                        * $weight->getValue() / $totalWeight);
                }
            }
        }

        return $foodstuff;
    }
}
