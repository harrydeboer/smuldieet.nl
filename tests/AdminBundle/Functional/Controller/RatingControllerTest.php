<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\RatingRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class RatingControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/waardering');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/waardering/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Rating opslaan');

        $form = $buttonCrawlerNode->form();

        $form['review[rating]'] = 8;
        $form['review[content]'] = 'testContent';
        $form['review[pending]'] = false;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waardering');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $rating = $ratingRepository->findOneBy(['content' => 'testContent']);
        $id = $rating->getId();

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedContent = 'testContent2';
        $form['review[content]'] = $updatedContent;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waardering');

        $rating = $ratingRepository->findOneBy(['content' => $updatedContent]);

        $this->assertEquals($updatedContent, $rating->getContent());

        $crawler = $this->client->request('GET', '/admin/waardering/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/waardering');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $this->assertNull($ratingRepository->find($id));
    }
}
