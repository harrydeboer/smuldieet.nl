<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\CommentType;
use App\Repository\CommentRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends Controller
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
        private readonly CommentRepositoryInterface $commentRepository,
    ) {
    }

    #[
        Route('/pagina-cms/{slug}', name: 'page_cms', defaults: ['pageComment' => '1']),
        Route('/pagina-cms/{slug}/{page<[1-9]\d*>}', name: 'page_comment_index_paginated'),
    ]
    public function viewPage(string $slug, int $pageComment): Response
    {
        $page = $this->pageRepository->getBySlug($slug);

        $formComment = null;
        if (!is_null($this->getUser())) {
            $formComment = $this->createForm(CommentType::class, null, [
                'action' => $this->generateUrl('page_comment_create', ['pageId' => $page->getId()]),
            ]);
        }

        return $this->render('page/view.html.twig', [
            'formComment' => $formComment?->createView(),
            'page' => $page,
            'paginatorComments' => $this->commentRepository->findCommentsFromPage($page->getId(), $pageComment),
            'isLoggedIn' => !is_null($this->getUser()),
        ]);
    }

    public function catchAll(): void
    {
        throw $this->createNotFoundException('Deze pagina bestaat niet.');
    }
}
