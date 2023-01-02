<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Tests\Factory\PageFactory;
use App\Repository\PageRepositoryInterface;
use App\Tests\Functional\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageRepositoryTest extends KernelTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $page = static::getContainer()->get(PageFactory::class)->create();

        $pageRepository = static::getContainer()->get(PageRepositoryInterface::class);

        $this->assertSame($page, $pageRepository->get($page->getId()));

        $updatedTitle = 'Test';
        $updatedSlug = 'test';
        $page->setTitle($updatedTitle);
        $page->setSlug($updatedSlug);

        $pageRepository->update();

        $this->assertSame($updatedTitle, $pageRepository->getByTitle($updatedTitle)->getTitle());
        $this->assertSame($updatedSlug, $pageRepository->getBySlug($updatedSlug)->getSlug());

        $id = $page->getId();
        $pageRepository->delete($page);

        $this->expectException(NotFoundHttpException::class);

        $pageRepository->get($id);
    }
}
