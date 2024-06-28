<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\CommentRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class PageControllerTest extends AuthVerifiedWebTestCase
{
    public function testCMSPage(): void
    {
        $this->client->request('GET', '/test');

        $this->assertResponseStatusCodeSame(404);

        $page = static::getContainer()->get(PageRepositoryInterface::class)->findOneBy(['title' => 'TestVerified']);

        $crawler = $this->client->request('GET', '/pagina-cms/' . $page->getSlug());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Plaatsen');

        $form = $buttonCrawlerNode->form();

        $content = 'test';
        $form['comment[content]'] = $content;

        $this->client->submit($form);

        $this->assertResponseRedirects('/pagina-cms/' . $page->getSlug());

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $comment = $commentRepository->findOneBy(['content' => $content, 'pending' => true]);

        $this->assertEquals($content, $comment->getContent());
    }
}
