<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Factory\PageFactory;
use App\Repository\PageRepositoryInterface;
use App\Tests\Functional\KernelTestCase;

class PageRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $page = static::getContainer()->get(PageFactory::class)->create();

        $pageRepository = static::getContainer()->get(PageRepositoryInterface::class);

        $this->assertSame($page, $pageRepository->find($page->getId()));

        $updatedTitle = 'Test';
        $updatedSlug = 'test';
        $page->setTitle($updatedTitle);
        $page->setSlug($updatedSlug);

        $pageRepository->update();

        $this->assertSame($updatedTitle, $pageRepository->getByTitle($updatedTitle)->getTitle());
        $this->assertSame($updatedSlug, $pageRepository->getBySlug($updatedSlug)->getSlug());

        $id = $page->getId();
        $pageRepository->delete($page);

        $this->assertNull($pageRepository->find($id));
    }
}
