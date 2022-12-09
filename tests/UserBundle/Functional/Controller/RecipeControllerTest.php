<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\RecipeRepositoryInterface;
use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\RecipeFactory;
use App\Tests\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class RecipeControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipeNotPending = static::getContainer()->get(RecipeFactory::class)->create(['isPending' => false]);
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)->create();

        $this->client->request('GET', '/user/recepten');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/user/recept/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Recept opslaan');

        $form = $buttonCrawlerNode->form();

        $testImagePath = __DIR__ . '/test.jpg';
        $form['recipe[image]'] = new File($testImagePath);
        $form['recipe[title]'] = 'test title';
        $form['recipe[ingredients]'] = 'test ingredient';
        $form['recipe[preparation_method]'] = 'test preparation';
        $form['recipe[is_self_invented]'] = 0;
        $form['recipe[number_of_persons]'] = 1;
        $form['recipe[cooking_time]'] = '0-10 min.';
        $form['recipe[kitchen]'] = 'Afrikaans';
        $form['recipe[type_of_dish]'] = 'Hoofdgerecht';

        $values = $form->getPhpValues();
        $values['recipe']['foodstuff_weights'] = [$foodstuff->getId() => 10];
        $values['recipe']['foodstuff_units'] = [$foodstuff->getId() => 'g'];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/user/recepten');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $recipe = $recipeRepository->findOneBy(['title' => 'test title']);
        $id = $recipe->getId();

        $this->client->xmlHttpRequest('GET', '/recept/zoeken/' . $recipe->getTitle());

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/uitloggen');

        $this->client->request('GET', '/recept/enkel/' . $id);

        $this->assertResponseStatusCodeSame(404);

        $this->client->request('GET', '/recept/enkel/' . $recipeNotPending->getId());

        $this->assertResponseIsSuccessful();

        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/user/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig recept');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'test2';
        $form['recipe[title]'] = $updatedTitle;

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/recepten');

        $this->client->request('GET', '/user/recepten/pagina/1');

        $this->assertResponseIsSuccessful();

        $recipeUpdate = $recipeRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $recipeUpdate->getTitle());

        $crawler = $this->client->request('GET', '/user/recept/wijzig/' . $recipeUpdate->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/recepten');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->assertNull($recipeRepository->find($id));
    }
}
