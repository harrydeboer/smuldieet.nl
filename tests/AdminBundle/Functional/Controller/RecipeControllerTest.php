<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\RecipeRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeControllerTest extends AuthAdminWebTestCase
{
    private function getRecipeRepository(): RecipeRepositoryInterface
    {
        return static::getContainer()->get(RecipeRepositoryInterface::class);
    }

    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/recepten');

        $this->assertResponseIsSuccessful();

        $recipe = static::getContainer()->get(RecipeRepositoryInterface::class)->findOneBy(['pending' => true]);

        $recipeRepository = $this->getContainer()->get(RecipeRepositoryInterface::class);

        $id = $recipe->getId();

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Goedkeuren');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipe = $recipeRepository->findOneBy(['pending' => false]);

        $this->assertFalse($recipe->isPending());

        $crawler = $this->client->request('GET', '/admin/recept/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/recepten');

        $recipeRepository = $this->getRecipeRepository();

        $this->expectException(NotFoundHttpException::class);

        $recipeRepository->get($id);
    }
}
