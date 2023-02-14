<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\RecipeRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use App\Tests\Factory\RecipeFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $buttonCrawlerNode = $crawler->selectButton('Goedkeuren');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipe = $recipeRepository->findOneBy(['pending' => false]);

        $this->assertEquals(false, $recipe->isPending());

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $this->expectException(NotFoundHttpException::class);

        $recipeRepository->get($id);
    }
}
