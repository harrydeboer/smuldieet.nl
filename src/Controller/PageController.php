<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    public function catchAll(Request $request): Response
    {
        $uri = explode('/', $request->getUri())[3];
        if ($uri) {
            if ($uri === 'home') {
                return $this->redirectToRoute('homepage');
            }

            return $this->render('page/view.html.twig', [
                'page' => $this->pageRepository->getBySlug($uri),
            ]);
        }

        throw new NotFoundHttpException('Deze pagina bestaat niet.');
    }
}
