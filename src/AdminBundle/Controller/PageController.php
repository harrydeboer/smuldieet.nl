<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\DeletePageType;
use App\AdminBundle\Form\PageType;
use App\Controller\AuthController;
use App\Entity\Page;
use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AuthController
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/pagina', name: 'adminPage')]
    public function view(): Response
    {
        $pages = $this->pageRepository->findAll();

        return $this->render('@AdminBundle/page/view.html.twig', [
            'pages' => $pages,
        ]);
    }

    #[Route('/pagina/wijzig/{id}', name: 'adminPageEdit')]
    public function edit(Request $request, Page $page): Response
    {
        $formUpdate = $this->createForm(PageType::class, $page, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeletePageType::class, $page, [
            'action' => $this->generateUrl('adminPageDelete', ['id' => $page->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->pageRepository->update();

            return $this->redirectToRoute('adminPage');
        }

        return $this->render('@AdminBundle/page/edit/view.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/pagina/toevoegen', name: 'adminPageCreate')]
    public function new(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setUser($this->getUser());
            $page->setTimestamp(time());
            $this->pageRepository->create($page);

            return $this->redirectToRoute('adminPage');
        }

        return $this->render('@AdminBundle/page/new/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pagina/verwijder/{id}', name: 'adminPageDelete')]
    public function delete(Request $request, Page $page): RedirectResponse
    {
        $form = $this->createForm(DeletePageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->delete($page);
        }

        return $this->redirectToRoute('adminPage');
    }
}
