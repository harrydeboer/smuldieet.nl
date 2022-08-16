<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Factory\RatingFactory;
use App\Repository\RatingRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class RatingControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/waardering');

        $this->assertResponseIsSuccessful();

        $rating = static::getContainer()->get(RatingFactory::class)->create(['pending' => true]);

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $id = $rating->getId();

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $form['review[pending]'] = false;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waardering');

        $rating = $ratingRepository->findOneBy(['pending' => false]);

        $this->assertEquals(false, $rating->getPending());

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waardering');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $this->assertNull($ratingRepository->find($id));
    }
}
