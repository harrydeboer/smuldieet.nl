<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\PageType;
use App\Controller\AuthController;
use App\Entity\Page;
use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AuthController
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    /**
     * The route has pagina as url and not pagina's because quotes are not to be used in urls.
     */
    #[Route('/paginas', name: 'admin_pages')]
    public function view(): Response
    {
        $pages = $this->pageRepository->findAll();

        return $this->render('@AdminBundle/page/view.html.twig', [
            'pages' => $pages,
        ]);
    }

    #[Route('/pagina/wijzig/{id}', name: 'admin_page_edit')]
    public function edit(Request $request, int $id): Response
    {
        $page = $this->pageRepository->get($id);

        $form = $this->createForm(PageType::class, $page, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $page, [
            'action' => $this->generateUrl('admin_page_delete', ['id' => $page->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setUpdatedAt(time());
            $this->pageRepository->update();

            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('@AdminBundle/page/edit.html.twig', [
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/pagina/toevoegen', name: 'admin_page_create')]
    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setUser($this->getUser());
            $page->setCreatedAt(time());
            $this->pageRepository->create($page);

            return $this->redirectToRoute('admin_pages');
        }

        return $this->render('@AdminBundle/page/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pagina/verwijder/{id}', name: 'admin_page_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $page = $this->pageRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->delete($page);
        }

        return $this->redirectToRoute('admin_pages');
    }
}
