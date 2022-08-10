<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\FoodstuffRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class FoodstuffControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/voedingsmiddel');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsmiddel opslaan');

        $form = $buttonCrawlerNode->form();

        $form['foodstuff[name]'] = 'test1';
        $form['foodstuff[energyKcal]'] = 80;
        $form['foodstuff[carbohydrates]'] = 20;
        $form['foodstuff[water]'] = 80;

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddel');

        $crawler = $this->client->request('GET', '/voedingsmiddel/van-voedingsmiddelen');

        $buttonCrawlerNode = $crawler->selectButton('Voedingsmiddel opslaan');

        $form = $buttonCrawlerNode->form();

        $form['foodstuff_from_foodstuffs[name]'] = 'test2';

        $values = $form->getPhpValues();
        $values['foodstuff_from_foodstuffs']['foodstuffs'] = [1];
        $values['foodstuff_from_foodstuffs']['foodstuffWeights'] = [100];
        $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertResponseRedirects('/voedingsmiddel');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $foodstuff = $foodstuffRepository->findOneBy(['name' => 'test1']);
        $id = $foodstuff->getId();

        $this->client->request('GET', '/voedingsmiddel/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig voedingsmiddel');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testUpdate';
        $form['foodstuff[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddel');

        $foodstuff = $foodstuffRepository->findOneBy(['name' => $updatedName]);

        $this->assertEquals($updatedName, $foodstuff->getName());

        $crawler = $this->client->request('GET', '/voedingsmiddel/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/voedingsmiddel');

        $foodstuffRepository = $this->getContainer()->get(FoodstuffRepositoryInterface::class);

        $this->assertNull($foodstuffRepository->find($id));
    }
}
