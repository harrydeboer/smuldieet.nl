<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Factory\PageFactory;
use App\Repository\DayRepositoryInterface;
use App\Tests\Functional\AuthAdminWebTestCase;

class DayControllerTest extends AuthAdminWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        static::getContainer()->get(PageFactory::class)->create(['title' => 'Dagboek']);
        $this->client->request('GET', '/dagboek');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Dag opslaan');

        $form = $buttonCrawlerNode->form();

        $form['day[date]'] = '01-01-2000';

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $crawler = $this->client->request('GET', '/dag/toevoegen/standaard');

        $buttonCrawlerNode = $crawler->selectButton('Standaard dag opslaan');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $dayRepository = $this->getContainer()->get(DayRepositoryInterface::class);

        $day = $dayRepository->findOneBy(['timestamp' => 946684800]);
        $id = $day->getId();

        $this->client->request('GET', '/dagboek/pagina/1');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/dag/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig dag');

        $form = $buttonCrawlerNode->form();

        $updatedDate = '01-01-2001';
        $form['day[date]'] = $updatedDate;

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $day = $dayRepository->findOneBy(['timestamp' => 978307200]);

        $this->assertEquals($updatedDate, $day->getDate());

        $crawler = $this->client->request('GET', '/dag/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $dayRepository = $this->getContainer()->get(DayRepositoryInterface::class);

        $this->assertNull($dayRepository->find($id));
    }
}
