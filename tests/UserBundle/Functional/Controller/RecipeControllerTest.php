<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipeNotPending = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['pending' => false]);
        $foodstuff = static::getContainer()
            ->get(FoodstuffRepositoryInterface::class)
            ->findOneBy(['name' => 'verified']);

        $this->client->request('GET', '/gebruiker/recepten');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/gebruiker/recept/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Recept opslaan');

        $form = $buttonCrawlerNode->form();

        $testImagePath = dirname(__DIR__, 4) . '/public/uploads/test/test.jpg';
        $form['recipe[image]'] = new File($testImagePath);
        $form['recipe[title]'] = 'test title';
        $form['recipe[ingredients]'] = 'test ingredient';
        $form['recipe[preparation_method]'] = 'test preparation';
        $form['recipe[self_invented]'] = 0;
        $form['recipe[number_of_persons]'] = 1;
        $form['recipe[cooking_time]'] = '0-10 min.';
        $form['recipe[kitchen]'] = 'Afrikaans';
        $form['recipe[type_of_dish]'] = 'Hoofdgerecht';

        $values = $form->getPhpValues();
        $values['recipe']['foodstuff_weights'][0]['foodstuff_id'] = $foodstuff->getId();
        $values['recipe']['foodstuff_weights'][0]['value'] = 10;
        $values['recipe']['foodstuff_weights'][0]['unit'] = 'g';

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/gebruiker/recepten');

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

        $crawler = $this->client->request('GET', '/gebruiker/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig recept');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'test2';
        $form['recipe[title]'] = $updatedTitle;

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/recepten');

        $this->client->request('GET', '/gebruiker/recepten/pagina/1');

        $this->assertResponseIsSuccessful();

        $recipeUpdate = $recipeRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $recipeUpdate->getTitle());

        $crawler = $this->client->request('GET', '/gebruiker/recept/wijzig/' . $recipeUpdate->getId());

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/recepten');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $recipeRepository->get($id);
    }
}
