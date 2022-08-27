<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Tests\Factory\RecipeFactory;
use App\Repository\RecipeRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class RecipeControllerTest extends AuthAdminWebTestCase
{
    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/recepten');

        $this->assertResponseIsSuccessful();

        $recipe = static::getContainer()->get(RecipeFactory::class)->create(['pending' => true]);

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $id = $recipe->getId();

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $form['recipe[pending]'] = false;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipe = $recipeRepository->findOneBy(['pending' => false]);

        $this->assertEquals(false, $recipe->getPending());

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->assertNull($recipeRepository->find($id));
    }
}
