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
            foreach ($params['recipeWeights'] as $weight) {
                $cookbook->removeRecipeWeight($weight);
                $cookbook->addRecipeWeight($weight);
            }
        } else {
            $weight = $this->recipeWeightFactory->create();
            $cookbook->addRecipeWeight($weight);
        }

        $this->cookbookRepository->update($cookbook, new ArrayCollection());

        return $cookbook;
    }
}
