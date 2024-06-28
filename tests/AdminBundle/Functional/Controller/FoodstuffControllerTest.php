<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodstuffControllerTest extends AuthAdminWebTestCase
{
    private function getFoodstuffRepository(): FoodstuffRepositoryInterface
    {
        return static::getContainer()->get(FoodstuffRepositoryInterface::class);
    }

    public function testUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->findOneBy(['name' => 'test']);

        $id = $foodstuff->getId();

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testTitle2';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddelen');

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/admin/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/voedingsmiddelen');

        $foodstuffRepository = $this->getFoodstuffRepository();

        $this->expectException(NotFoundHttpException::class);

        $foodstuffRepository->get($id);
    }
}
