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

        $updatedDisplayName = 'nutrientDisplayName';

        $this->assertSame($nutrient, $nutrientRepository->get($nutrient->getId()));

        $nutrient->setDisplayName($updatedDisplayName);
        $nutrientRepository->update();

        $id = $nutrient->getId();
        $this->assertSame($updatedDisplayName,
            $nutrientRepository->findOneBy(['displayName' => $updatedDisplayName])->getDisplayName());

        $nutrientRepository->delete($nutrient);

        $this->expectException(NotFoundHttpException::class);

        $nutrientRepository->get($id);
    }
}
