<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\RatingRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RatingControllerTest extends AuthVerifiedWebTestCase
{
    private function getRatingRepository(): RatingRepositoryInterface
    {
        return static::getContainer()->get(RatingRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $recipe = static::getContainer()->get(RecipeRepositoryInterface::class)->findOneBy(['title' => 'test']);

        $this->client->request('GET', '/gebruiker/waarderingen');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('nav', 'Waarderingen');

        $crawler = $this->client->request('GET', '/gebruiker/recensie/' . $recipe->getId());

        $buttonCrawlerNode = $crawler->selectButton('Recensie opslaan');

        $form = $buttonCrawlerNode->form();
        $form['review[rating]'] = 9;
        $form['review[content]'] = 'test';

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/waarderingen');

        $ratingRepository = $this->getContainer()->get(RatingRepositoryInterface::class);

        $review = $ratingRepository->findAllPendingReviews()[1];
        $id = $review->getId();

        $this->client->request('GET', '/gebruiker/recensie/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/gebruiker/recensie/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig recensie');

        $form = $buttonCrawlerNode->form();

        $updatedContent = 'test2';
        $form['review[content]'] = $updatedContent;

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/waarderingen');

        $reviewUpdate = $ratingRepository->findOneBy(['content' => $updatedContent]);

        $this->assertEquals($updatedContent, $reviewUpdate->getContent());

        $crawler = $this->client->request('GET', '/gebruiker/recensie/wijzig/' . $reviewUpdate->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/waarderingen');

        $ratingRepository = $this->getRatingRepository();

        $this->expectException(NotFoundHttpException::class);

        $ratingRepository->get($id);
    }
}
