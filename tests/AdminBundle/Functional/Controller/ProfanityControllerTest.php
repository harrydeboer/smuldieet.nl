<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\ProfanityRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfanityControllerTest extends AuthAdminWebTestCase
{
    private function getProfanityRepository(): ProfanityRepositoryInterface
    {
        return static::getContainer()->get(ProfanityRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/scheldwoorden');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/scheldwoord/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Scheldwoord opslaan');

        $form = $buttonCrawlerNode->form();

        $form['profanity[name]'] = 'testName';

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/scheldwoorden');

        $profanityRepository = $this->getContainer()->get(ProfanityRepositoryInterface::class);

        $profanity = $profanityRepository->findOneBy(['name' => 'testName']);
        $id = $profanity->getId();

        $crawler = $this->client->request('GET', '/admin/scheldwoord/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedName = 'testName2';
        $form['profanity[name]'] = $updatedName;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/scheldwoorden');

        $profanity = $profanityRepository->findOneBy(['name' => 'testName2']);

        $this->assertEquals($updatedName, $profanity->getName());

        $crawler = $this->client->request('GET', '/admin/scheldwoord/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/scheldwoorden');

        $profanityRepository = $this->getProfanityRepository();

        $this->expectException(NotFoundHttpException::class);

        $profanityRepository->get($id);
    }
}
