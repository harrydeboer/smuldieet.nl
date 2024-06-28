<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\CookbookRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CookbookRepositoryTest extends KernelTestCase
{
    private function getCookbookRepository(): CookbookRepositoryInterface
    {
        return static::getContainer()->get(CookbookRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $cookbookRepository = $this->getCookbookRepository();

        $cookbook = $cookbookRepository->findOneBy(['title' => 'Test']);

        $this->assertSame($cookbook, $cookbookRepository->find($cookbook->getId()));

        $oldRecipeWeights = new ArrayCollection();
        foreach ($cookbook->getRecipeWeights() as $weight) {
            $oldRecipeWeights->add($weight);
        }

        $updatedTitle = 'Test';
        $cookbook->setTitle($updatedTitle);

        $cookbookRepository->update($cookbook, $oldRecipeWeights);

        $this->assertSame($updatedTitle, $cookbookRepository->findOneBy(['title' => $updatedTitle])->getTitle());

        $id = $cookbook->getId();

        $this->assertSame($cookbook, $cookbookRepository->getFromUser($id, $cookbook->getUser()->getId()));

        $cookbookRepository->delete($cookbook);

        $this->assertNull($cookbookRepository->find($id));
    }
}
