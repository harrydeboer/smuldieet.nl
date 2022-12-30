<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Cookbook;
use App\Repository\CookbookRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CookbookFactory extends AbstractFactory
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeWeightFactory $recipeWeightFactory,
    ) {
    }

    public function create(array $params = []): Cookbook
    {
        $user = $params['user'] ?? $this->userFactory->create();

        $cookbook = new Cookbook();
        $cookbook->setTitle(uniqid('cookbook'));
        $cookbook->setTimestamp(time());
        $cookbook->setUser($user);

        $this->setParams($params, $cookbook);

        $this->cookbookRepository->create($cookbook);

        if (isset($params['recipeWeights'])) {
            $recipeWeights = $params['recipeWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->recipeWeightFactory->create(['cookbook' => $cookbook]);
            $arrayCollection->add($weight);
            $recipeWeights = $arrayCollection;
        }

        $cookbook->setRecipeWeights($recipeWeights);
        $this->cookbookRepository->update($cookbook);

        return $cookbook;
    }
}
