<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class RecipeControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/recept');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/recept/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Recept opslaan');

        $form = $buttonCrawlerNode->form();

        $form['recipe[title]'] = 'testTitle';
        $form['recipe[user]'] = $this->user->getId();
        $form['recipe[pending]'] = false;
        $form['recipe[preparationMethod]'] = 'test';
        $form['recipe[niceStory]'] = 'test';
        $form['recipe[isSelfInvented]'] = 0;
        $form['recipe[numberOfPersons]'] = 1;
        $form['recipe[cookingTime]'] = '0-10 min.';
        $form['recipe[typeOfDish]'] = 'Hoofdgerecht';
        $form['recipe[kitchen]'] = 'Afrikaans';

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recept');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $recipe = $recipeRepository->findOneBy(['title' => 'testTitle']);
        $id = $recipe->getId();

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'testTitle2';
        $form['recipe[title]'] = $updatedTitle;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recept');

        $recipe = $recipeRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $recipe->getTitle());

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recept');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->assertNull($recipeRepository->find($id));
    }
}
