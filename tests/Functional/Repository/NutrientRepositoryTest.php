<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\NutrientRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NutrientRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $nutrientRepository = static::getContainer()->get(NutrientRepositoryInterface::class);
        $nutrient = $nutrientRepository->findOneBy(['name' => 'protein']);

        $updatedNameNL = 'nutrient';

        $this->assertSame($nutrient, $nutrientRepository->get($nutrient->getId()));

        $nutrient->setName($updatedNameNL);
        $nutrientRepository->update();

        $id = $nutrient->getId();
        $this->assertSame($updatedNameNL, $nutrientRepository->findOneBy(['name' => $updatedNameNL])->getName());

        $nutrientRepository->delete($nutrient);

        $this->expectException(NotFoundHttpException::class);

        $nutrientRepository->get($id);
    }
}
