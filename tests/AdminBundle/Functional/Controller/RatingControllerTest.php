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
        $this->client->request('GET', '/admin/waarderingen');

        $this->assertResponseIsSuccessful();

        $rating = static::getContainer()->get(RatingFactory::class)->create(['isPending' => true]);

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $id = $rating->getId();

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $form['review[is_pending]'] = false;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waarderingen');

        $rating = $ratingRepository->findOneBy(['isPending' => false]);

        $this->assertEquals(false, $rating->getIsPending());

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waarderingen');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $ratingRepository->get($id);
    }
}
