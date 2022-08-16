<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class RecipeControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/recept');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/recept/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Recept opslaan');

        $form = $buttonCrawlerNode->form();

        $testImagePath = __DIR__ . '/test.jpg';
        $form['recipe[image]'] = new File($testImagePath);
        $form['recipe[title]'] = 'test';
        $form['recipe[preparationMethod]'] = 'test';
        $form['recipe[niceStory]'] = 'test';
        $form['recipe[isSelfInvented]'] = 0;
        $form['recipe[numberOfPersons]'] = 1;
        $form['recipe[cookingTime]'] = '0-10 min.';
        $form['recipe[kitchen]'] = 'Afrikaans';
        $form['recipe[typeOfDish]'] = 'Hoofdgerecht';

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $recipe = $recipeRepository->findOneBy(['title' => 'test']);
        $id = $recipe->getId();

        $this->client->request('GET', '/recept/zoeken/1/' . $recipe->getTitle());

        $this->assertResponseIsSuccessful();

        $this->client->xmlHttpRequest('GET', '/recept/zoeken/' . $recipe->getTitle() . '/1');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig recept');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'test2';
        $form['recipe[title]'] = $updatedTitle;

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept');

        $this->client->request('GET', '/recept/pagina/1');

        $this->assertResponseIsSuccessful();

        $recipe = $recipeRepository->findOneBy(['title' => $updatedTitle]);

        $ratingOld = $recipe->getRating();
        $votesOld = $recipe->getVotes();

        $this->assertEquals($updatedTitle, $recipe->getTitle());

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipe->getId());

        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Waardeer');

        $form = $buttonCrawlerNode->form();

        $rating = 9;
        $form['rating[rating]'] = $rating;

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipe->getId());

        $crawler = $this->client->request('GET', '/recept/enkel/' . $recipe->getId());

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipe = $recipeRepository->findOneBy(['title' => $updatedTitle]);
        $recipeRating = $recipe->getRating();

        $this->assertEquals(($rating + $votesOld * $ratingOld) / ($votesOld + 1), $recipeRating);
        $this->assertEquals($votesOld + 1, $recipe->getVotes());

        $buttonCrawlerNode = $crawler->selectButton('Waardeer');

        $form = $buttonCrawlerNode->form();

        $ratingUpdate = 8;
        $form['rating[rating]'] = $ratingUpdate;
        $form['rating[content]'] = 'test';

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept/enkel/' . $recipe->getId());

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipeUpdate = $recipeRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($ratingUpdate - $rating + $recipeRating, $recipeUpdate->getRating());
        $this->assertEquals($votesOld + 1, $recipe->getVotes());

        $crawler = $this->client->request('GET', '/recept/wijzig/' . $recipeUpdate->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/recept');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->assertNull($recipeRepository->find($id));
    }
}
