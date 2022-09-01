<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\RecipeFactory;
use App\Repository\CookbookRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class CookbookControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe1 = static::getContainer()->get(RecipeFactory::class)->create();
        $recipe2 = static::getContainer()->get(RecipeFactory::class)->create();
        $timesSavedOld1 = $recipe1->getTimesSaved();
        $timesSavedOld2 = $recipe2->getTimesSaved();

        $this->client->request('GET', '/kookboeken');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/kookboek/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Kookboek opslaan');

        $form = $buttonCrawlerNode->form();

        $form['cookbook[title]'] = 'test';

        $values = $form->getPhpValues();
        $values['cookbook']['recipeChoices'] = [$recipe1->getId() => 1, $recipe2->getId() => 1];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/kookboeken');

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $cookbook = $cookbookRepository->findOneBy(['title' => 'test']);
        $id = $cookbook->getId();

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipe1 = $recipeRepository->get($recipe1->getId());
        $recipe2 = $recipeRepository->get($recipe2->getId());
        $this->assertEquals(1 + $timesSavedOld1, $recipe1->getTimesSaved());
        $this->assertEquals(1 + $timesSavedOld2, $recipe2->getTimesSaved());

        $this->client->request('GET', '/kookboek/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig kookboek');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'Test';
        $form['cookbook[title]'] = $updatedTitle;

        $values = $form->getPhpValues();
        $values['cookbook']['recipeChoices'] = [$recipe1->getId() => 1];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/kookboeken');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipe1 = $recipeRepository->get($recipe1->getId());
        $recipe2 = $recipeRepository->get($recipe2->getId());
        $this->assertEquals($timesSavedOld1 + 1, $recipe1->getTimesSaved());
        $this->assertEquals($timesSavedOld2, $recipe2->getTimesSaved());

        $cookbook = $cookbookRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $cookbook->getTitle());

        $crawler = $this->client->request('GET', '/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/kookboeken');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);
        $recipe1 = $recipeRepository->get($recipe1->getId());
        $recipe2 = $recipeRepository->get($recipe2->getId());
        $this->assertEquals($timesSavedOld1, $recipe1->getTimesSaved());
        $this->assertEquals($timesSavedOld2, $recipe2->getTimesSaved());

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $this->assertNull($cookbookRepository->find($id));
    }
}
