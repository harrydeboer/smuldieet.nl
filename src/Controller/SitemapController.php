<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PageRepositoryInterface;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class SitemapController extends Controller
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/sitemap', name: 'sitemap')]
    public function view(): Response
    {
        /**
         * @noinspection HttpUrlsUsage
         * Using https directly does not work.
         * The http link redirects to https so is safe.
         */
        $sitemap = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<?xml-stylesheet type="text/xsl" href="/css/sitemap.xsl' . '"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
        );

        $baseUrl = match ($this->kernel->getEnvironment()) {
            'dev', 'test' => "http://smuldieet",
            'prod' => "https://smuldieet.nl",
            'staging' => "https://staging.smuldieet.nl",
        };

        $update = '2022-08-09';

        $pageSlugs = ['/'];

        foreach ($this->pageRepository->findAll() as $page) {
            if ($page->getSlug() === 'home'
                || $page->getSlug() === 'dagen'
                || $page->getSlug() === 'kookboeken'
                || $page->getSlug() === 'overzicht'
                || $page->getSlug() === 'recepten'
            ) {
                continue;
            }
            $pageSlugs[] = '/' . $page->getSlug();
        }

        foreach ($pageSlugs as $pageSlug) {
            $url = $sitemap->addChild('url');
            $url->addChild('loc', $baseUrl . $pageSlug);
            $url->addChild('priority', '1.0');
            $url->addChild('lastmod', $update);
            $url->addChild('changefreq', 'monthly');
        }

        $sitemapXML =  $sitemap->saveXML();

        $response = new Response($sitemapXML);
        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
