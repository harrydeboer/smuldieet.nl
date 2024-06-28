<?php

declare(strict_types=1);

namespace App\Tests\AdminBundle\Functional\Controller;

use App\Repository\PageRepositoryInterface;
use App\Tests\AdminBundle\Functional\AuthAdminWebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageControllerTest extends AuthAdminWebTestCase
{
    private function getPageRepository(): PageRepositoryInterface
    {
        return static::getContainer()->get(PageRepositoryInterface::class);
    }

    public function testCreateUpdateDelete(): void
    {
        $this->client->request('GET', '/admin/paginas');

        $this->assertResponseIsSuccessful();

        $crawler = $this->client->request('GET', '/admin/pagina/toevoegen');

        $buttonCrawlerNode = $crawler->selectButton('Pagina opslaan');

        $form = $buttonCrawlerNode->form();

        $form['page[title]'] = 'testTitle';
        $form['page[slug]'] = 'testSlug';
        $form['page[summary]'] = 'testSum';
        $form['page[content]'] = 'testContent';

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/paginas');

        $pageRepository = $this->getContainer()->get(PageRepositoryInterface::class);

        $page = $pageRepository->findOneBy(['slug' => 'testSlug']);
        $id = $page->getId();

        $crawler = $this->client->request('GET', '/admin/pagina/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Wijzig');

        $form = $buttonCrawlerNode->form();

        $updatedTitle = 'testTitle2';
        $form['page[title]'] = $updatedTitle;

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/paginas');

        $page = $pageRepository->findOneBy(['slug' => 'testSlug']);

        $this->assertEquals($updatedTitle, $page->getTitle());

        $crawler = $this->client->request('GET', '/admin/pagina/wijzig/' . $id);

        $buttonCrawlerNode = $crawler->selectButton('Verwijder');

        $form = $buttonCrawlerNode->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/paginas');

        $pageRepository = $this->getPageRepository();

        $this->expectException(NotFoundHttpException::class);

        $pageRepository->get($id);
    }
}
