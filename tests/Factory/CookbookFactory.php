<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Cookbook;
use App\Repository\CookbookRepositoryInterface;

class CookbookFactory extends AbstractFactory
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly UserFactory $userFactory,
        private readonly CookbookRecipeWeightFactory $recipeWeightFactory,
    ) {
    }

    public function create(array $params = []): Cookbook
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $cookbook = new Cookbook();
        $cookbook->setTitle(uniqid('cookbook'));
        $cookbook->setCreatedAt(time());
        $cookbook->setUser($user);

        if (!isset($params['recipeWeights'])) {
            $weight = $this->recipeWeightFactory->create();
            $cookbook->addRecipeWeight($weight);
        }

        $this->setParams($params, $cookbook);

        return $this->cookbookRepository->create($cookbook);
    }
}
