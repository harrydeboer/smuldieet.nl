<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Cookbook;
use App\Repository\CookbookRepositoryInterface;

class CookbookFactory extends AbstractFactory
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly UserFactory $userFactory,
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
        $cookbook = new Cookbook();
        $cookbook->setTitle(uniqid('cookbook'));
        $cookbook->setTimestamp(time());
        $cookbook->setUser($paramsParent['user']);

        $this->setParams($params, $cookbook);

        return $this->cookbookRepository->create($cookbook);
    }
}
