<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\CommentRepositoryInterface;
use App\Tests\Factory\PageFactory;
use App\Tests\Functional\AuthUserWebTestCase;

class PageControllerTest extends AuthUserWebTestCase
{
    public function testCMSPage(): void
    {
        $this->client->request('GET', '/test');

        $this->assertResponseStatusCodeSame(404);

        $page = static::getContainer()->get(PageFactory::class)->create(['title' => 'Test', 'slug' => 'test']);

        $crawler = $this->client->request('GET', '/pagina-cms/' . $page->getSlug());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Plaatsen');

        $form = $buttonCrawlerNode->form();

        $content = 'test';
        $form['comment[content]'] = $content;

        $this->client->submit($form);

        $this->assertResponseRedirects('/pagina-cms/' . $page->getSlug());

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $comment = $commentRepository->findOneBy(['content' => $content, 'isPending' => true]);

        $this->assertEquals($content, $comment->getContent());
    }
}
