<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Cookbook;
use App\Repository\CookbookRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CookbookFactory extends AbstractFactory
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly UserFactory $userFactory,
        private readonly RecipeFactory $recipeFactory,
    ) {
    }

    public function create(array $params = []): Cookbook
    {
        $paramsParent = [];
        if (isset($params['user'])) {
            $paramsParent['user'] = $params['user'];
        } else {
            $paramsParent['user'] = $this->userFactory->create();
        }
        if (isset($params['recipes'])) {
            $paramsParent['recipes'] = $params['recipes'];
        } else {
            $arrayCollection = new ArrayCollection();
            $arrayCollection->add($this->recipeFactory->create());
            $paramsParent['recipes'] = $arrayCollection;
        }
        $cookbook = new Cookbook();
        $cookbook->setTitle(uniqid('cookbook'));
        $cookbook->setTimestamp(time());
        $cookbook->setUser($paramsParent['user']);
        $cookbook->setRecipes($paramsParent['recipes']);

        $this->setParams($params, $cookbook);

        return $this->cookbookRepository->create($cookbook);
    }
}
