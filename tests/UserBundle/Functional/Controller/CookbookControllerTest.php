<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Repository\CookbookRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthVerifiedWebTestCase;

class CookbookControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe1 = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['title' => 'test']);
        $recipe2 = static::getContainer()
            ->get(RecipeRepositoryInterface::class)
            ->findOneBy(['title' => 'testVerified']);

        $this->client->request('GET', '/gebruiker/kookboeken');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/gebruiker/kookboek/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Kookboek opslaan');

        $form = $buttonCrawlerNode->form();

        $form['cookbook[title]'] = 'testUserPanel';

        $values = $form->getPhpValues();
        $values['cookbook']['recipe_weights'][0]['recipe_id'] = $recipe1->getId();
        $values['cookbook']['recipe_weights'][0]['value'] = 1;
        $values['cookbook']['recipe_weights'][1]['recipe_id'] = $recipe2->getId();
        $values['cookbook']['recipe_weights'][1]['value'] = 1;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/gebruiker/kookboeken');

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $cookbook = $cookbookRepository->findOneBy(['title' => 'testUserPanel']);
        $id = $cookbook->getId();

        $this->client->request('GET', '/gebruiker/kookboek/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/gebruiker/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig kookboek');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'updateUserPanel';
        $form['cookbook[title]'] = $updatedTitle;

        $values = $form->getPhpValues();
        $values['cookbook']['recipe_weights'][0]['recipe_id'] = $recipe1->getId();
        $values['cookbook']['recipe_weights'][0]['value'] = 1;

        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/gebruiker/kookboeken');

        $cookbook = $cookbookRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $cookbook->getTitle());

        $crawler = $this->client->request('GET', '/gebruiker/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/gebruiker/kookboeken');

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $this->assertNull($cookbookRepository->find($id));
    }
}
