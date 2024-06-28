<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\CommentRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class RecipeControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipePending = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['pending' => true]);
        $recipeNotPending = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['pending' => false]);

        $this->client->xmlHttpRequest('GET', '/recept/zoeken/' . $recipeNotPending->getTitle());

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/uitloggen');

        $this->client->request('GET', '/recept/enkel/' . $recipePending->getId());

        $this->assertResponseStatusCodeSame(404);

        $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $this->assertResponseIsSuccessful();

        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Bewaar');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipeNotPending->getId());

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipeNotPending->getId());

        $ratingOld = $recipeNotPending->getRating();
        $votesOld = $recipeNotPending->getVotes();

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Waardeer');

        $form = $buttonCrawlerNode->form();

        $rating = 6;
        $form['rating[rating]'] = $rating;

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipeNotPending->getId());

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipe = $recipeRepository->findOneBy(['title' => $recipeNotPending->getTitle()]);
        $recipeRating = $recipe->getRating();

        $this->assertEquals(($rating + $votesOld * $ratingOld) / ($votesOld + 1), $recipeRating);
        $this->assertEquals($votesOld + 1, $recipe->getVotes());

        $buttonCrawlerNode = $crawler->selectButton('Waardeer');

        $form = $buttonCrawlerNode->form();

        $ratingUpdate = 5;
        $form['rating[rating]'] = $ratingUpdate;

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipe->getId());

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipeUpdate = $recipeRepository->findOneBy(['title' => $recipe->getTitle()]);

        $this->assertEquals(($ratingUpdate - $rating + $recipeRating * ($votesOld + 1)) / ($votesOld + 1),
            $recipeUpdate->getRating());
        $this->assertEquals($votesOld + 1, $recipeUpdate->getVotes());

        $this->client->request('GET', '/recept/enkel/' . $recipe->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder waardering');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipe->getId());

        $buttonCrawlerNode = $crawler->selectButton('Plaatsen');

        $form = $buttonCrawlerNode->form();

        $content = 'test';
        $form['comment[content]'] = $content;

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipe->getId() . '#comments');

        $commentRepository = $this->getContainer()->get(CommentRepositoryInterface::class);

        $comment = $commentRepository->findOneBy(['content' => $content, 'pending' => true]);

        $this->assertEquals($content, $comment->getContent());
    }
}
