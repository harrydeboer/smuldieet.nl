<?php

declare(strict_types=1);

namespace App\Tests\UserBundle\Functional\Controller;

use App\Tests\Factory\RecipeFactory;
use App\Repository\CookbookRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class CookbookControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $recipe1 = static::getContainer()->get(RecipeFactory::class)->create();
        $recipe2 = static::getContainer()->get(RecipeFactory::class)->create();

        $this->client->request('GET', '/user/kookboeken');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/user/kookboek/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Kookboek opslaan');

        $form = $buttonCrawlerNode->form();

        $form['cookbook[title]'] = 'test';

        $values = $form->getPhpValues();
        $values['cookbook']['recipe_weights'] = [$recipe1->getId() => 1, $recipe2->getId() => 1];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/user/kookboeken');

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $cookbook = $cookbookRepository->findOneBy(['title' => 'test']);
        $id = $cookbook->getId();

        $this->client->request('GET', '/user/kookboek/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/user/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig kookboek');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'Test';
        $form['cookbook[title]'] = $updatedTitle;

        $values = $form->getPhpValues();
        $values['cookbook']['recipe_weights'] = [$recipe1->getId() => 1];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/user/kookboeken');

        $cookbook = $cookbookRepository->findOneBy(['title' => $updatedTitle]);

        $this->assertEquals($updatedTitle, $cookbook->getTitle());

        $crawler = $this->client->request('GET', '/user/kookboek/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/user/kookboeken');

        $cookbookRepository = $this->getContainer()->get(CookbookRepositoryInterface::class);

        $this->assertNull($cookbookRepository->find($id));
    }
}
