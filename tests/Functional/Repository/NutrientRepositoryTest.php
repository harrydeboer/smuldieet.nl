<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\NutrientRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NutrientRepositoryTest extends KernelTestCase
{
    private function getNutrientRepository(): NutrientRepositoryInterface
    {
        return static::getContainer()->get(NutrientRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $nutrientRepository = $this->getNutrientRepository();

        $nutrient = $nutrientRepository->findOneBy(['name' => 'water']);

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
