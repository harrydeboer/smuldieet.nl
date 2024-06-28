<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\CommentRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/commentaar');

        $this->assertResponseIsSuccessful();

        $comment = static::getContainer()->get(CommentRepositoryInterface::class)->findOneBy(['pending' => true]);

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $id = $comment->getId();

        $crawler = $this->client->request('GET', '/admin/commentaar/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Goedkeuren');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/commentaar');

        $comment = $commentRepository->findOneBy(['pending' => false]);

        $this->assertFalse($comment->isPending());

        $crawler = $this->client->request('GET', '/admin/commentaar/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/commentaar');

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $commentRepository->get($id);
    }
}
