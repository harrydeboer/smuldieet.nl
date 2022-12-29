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
        $cookbook = new Cookbook();

        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }

        $cookbook->setTitle(uniqid('cookbook'));
        $cookbook->setTimestamp(time());
        $cookbook->setUser($paramsParent['user']);

        $this->setParams($params, $cookbook);

        $this->cookbookRepository->create($cookbook);

        if (isset($params['recipeWeights'])) {
            $paramsParent['recipeWeights'] = $params['recipeWeights'];
        } else {
            $arrayCollection = new ArrayCollection();
            $weight = $this->recipeWeightFactory->create(['cookbook' => $cookbook]);
            $arrayCollection->add($weight);
            $paramsParent['recipeWeights'] = $arrayCollection;
        }

        $cookbook->setRecipeWeights($paramsParent['recipeWeights']);
        $this->cookbookRepository->update($cookbook);

        return $cookbook;
    }
}
