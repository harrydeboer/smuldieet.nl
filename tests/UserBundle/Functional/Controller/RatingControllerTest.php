<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\RatingRepositoryInterface;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['pending' => false]);

        $this->client->request('GET', '/user/waarderingen');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Waarderingen');

        $crawler = $this->client->request('GET', '/user/recensie/' . $recipe->getId());

        $buttonCrawlerNode = $crawler->selectButton('Recensie opslaan');

        $form = $buttonCrawlerNode->form();
        $form['review[rating]'] = 9;
        $form['review[content]'] = 'test';

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/waarderingen');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $review = $ratingRepository->findAllPendingReviews()[0];
        $id = $review->getId();

        $this->client->request('GET', '/user/recensie/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/user/recensie/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig recensie');

        $form = $buttonCrawlerNode->form();

        $updatedContent = 'test2';
        $form['review[content]'] = $updatedContent;

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/waarderingen');

        $reviewUpdate = $ratingRepository->findOneBy(['content' => $updatedContent]);

        $this->assertEquals($updatedContent, $reviewUpdate->getContent());

        $crawler = $this->client->request('GET', '/user/recensie/wijzig/' . $reviewUpdate->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/waarderingen');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $ratingRepository->get($id);
    }
}
