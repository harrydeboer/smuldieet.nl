<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\RatingRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use App\Tests\Factory\RatingFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/recensies');

        $this->assertResponseIsSuccessful();

        $rating = static::getContainer()->get(RatingFactory::class)->create(['pending' => true]);

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $id = $rating->getId();

        $crawler = $this->client->request('GET', '/admin/recensie/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Goedkeuren');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recensies');

        $rating = $ratingRepository->findOneBy(['pending' => false]);

        $this->assertFalse($rating->isPending());

        $crawler = $this->client->request('GET', '/admin/recensie/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recensies');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $ratingRepository->get($id);
    }
}
