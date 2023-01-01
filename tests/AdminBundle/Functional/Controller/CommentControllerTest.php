<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\CommentRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use App\Tests\Factory\CommentFactory;

class CommentControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/commentaar');

        $this->assertResponseIsSuccessful();

        $comment = static::getContainer()->get(CommentFactory::class)->create(['isPending' => true]);

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $id = $comment->getId();

        $crawler = $this->client->request('GET', '/admin/commentaar/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $form['comment[is_pending]'] = false;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/commentaar');

        $comment = $commentRepository->findOneBy(['isPending' => false]);

        $this->assertEquals(false, $comment->getIsPending());

        $crawler = $this->client->request('GET', '/admin/commentaar/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/commentaar');

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $this->assertNull($commentRepository->find($id));
    }
}
