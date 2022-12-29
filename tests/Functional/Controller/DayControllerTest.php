<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Repository\DayRepositoryInterface;
use App\Tests\Factory\PageFactory;
use App\Tests\Functional\AuthVerifiedWebTestCase;
use DateTime;

class DayControllerTest extends AuthVerifiedWebTestCase
{
    public function testCreateUpdateDelete(): void
    {
        static::getContainer()->get(PageFactory::class)->create(['title' => 'Dagboek']);
        $this->client->request('GET', '/dagboek');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Dag opslaan');

        $form = $buttonCrawlerNode->form();

        $date = new DateTime();
        $date->setTimestamp(strtotime(date('Y') . '-01-01'));
        $form['day[date]'] = date('Y') . '-01-01';

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $crawler = $this->client->request('GET', '/dag/toevoegen/standaard');

        $buttonCrawlerNode = $crawler->selectButton('Standaard dag opslaan');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $dayRepository = $this->getContainer()->get(DayRepositoryInterface::class);

        $day = $dayRepository->findOneBy(['timestamp' => $date->getTimestamp()]);
        $id = $day->getId();

        $this->client->request('GET', '/dagboek/pagina/1');

        $this->assertResponseIsSuccessful();

        $this->client->request('GET', '/dag/enkel/' . $id);

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/dag/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig dag');

        $form = $buttonCrawlerNode->form();

        $updatedDate = new DateTime();
        $updatedDate->setTimestamp(strtotime(date('Y') . '-01-02'));
        $form['day[date]'] = date('Y') . '-01-02';

        $this->client->submit($form);

        $this->assertResponseRedirects('/dagboek');

        $day = $dayRepository->findOneBy(['timestamp' => $updatedDate->getTimestamp()]);

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
