<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository;

use App\Repository\PageRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageRepositoryTest extends KernelTestCase
{
    private function getPageRepository(): PageRepositoryInterface
    {
        return static::getContainer()->get(PageRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $pageRepository = $this->getPageRepository();

        $page = $pageRepository->findOneBy(['slug' => 'test']);

        $this->assertSame($page, $pageRepository->get($page->getId()));

        $updatedTitle = 'TestUpdate';
        $updatedSlug = 'testupdate';
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
