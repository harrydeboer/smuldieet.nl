<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Factory\FoodstuffFactory;
use App\Tests\Factory\PageFactory;
use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class FoodstuffControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        static::getContainer()->get(PageFactory::class)->create(['title' => 'Voedingsmiddelen']);
        $foodstuff = static::getContainer()->get(FoodstuffFactory::class)
            ->create(['user' => $this->user]);
        $foodstuffAnonymous = static::getContainer()->get(FoodstuffFactory::class)
            ->create();

        $this->client->request('GET', '/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/van-voedingsmiddelen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsmiddel opslaan');

        $form = $buttonCrawlerNode->form();

        $form['foodstuff_from_foodstuffs[name]'] = 'test2';

        $values = $form->getPhpValues();
        $weights = [];
        $weights[$foodstuff->getId()] = 100;
        $values['foodstuff_from_foodstuffs']['foodstuff_weights'] = $weights;
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuffCombined = $foodstuffRepository->getByName('test2');

        $this->assertResponseRedirects('/voedingsmiddel/wijzig/' . $foodstuffCombined->getId());

        $this->client->request('GET', '/uitloggen');

        $this->client->request('GET', '/voedingsmiddelen');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/voedingsmiddelen/letter/A');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/voedingsmiddel/enkel/' . $foodstuffAnonymous->getId());

        $this->assertResponseIsSuccessful();

        $this->client->loginUser($this->user);

        $this->client->xmlHttpRequest('GET', '/voedingsmiddel/zoeken/test2');

        $this->assertResponseIsSuccessful();

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $foodstuff->getName()]);
        $id = $foodstuff->getId();

        $this->client->request('GET', '/voedingsmiddel/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig voedingsmiddel');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testUpdate';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddelen');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddelen');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertNull($foodstuffRepository->find($id));
    }
}
