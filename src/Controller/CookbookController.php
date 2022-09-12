<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Cookbook;
use App\Form\CookbookType;
use App\Form\DeleteType;
use App\Repository\CookbookRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\AddRecipesService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookbookController extends AuthController
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly AddRecipesService $addRecipesService,
    ) {
    }

    #[
        Route('/kookboeken', name: 'cookbook'),
    ]
    public function view(): Response
    {
        $cookbooks = $this->getUser()->getCookbooks();

        return $this->render('cookbook/view.html.twig', [
            'cookbooks' => $cookbooks,
            'page' => $this->pageRepository->findOneBy(['title' => 'Kookboeken']),
        ]);
    }

    #[Route('/kookboek/wijzig/{id}', name: 'cookbook_edit')]
    public function edit(Request $request, int $id): Response
    {
        $cookbook = $this->getCookbook($id);

        $formUpdate = $this->createForm(CookbookType::class, $cookbook, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $cookbook, [
            'action' => $this->generateUrl('cookbook_delete', ['id' => $cookbook->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->cookbookRepository->update($cookbook);

            return $this->redirectToRoute('cookbook');
        } else {

            /**
             * When the form is not valid it only has recipe weights but not the recipes
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addRecipesService->addRecipesAndValidate($cookbook);
        }

        return $this->render('cookbook/edit.html.twig', [
            'cookbook' => $cookbook,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/kookboek/toevoegen', name: 'cookbook_create')]
    public function new(Request $request): Response
    {
        $cookbook = new Cookbook();
        $form = $this->createForm(CookbookType::class, $cookbook);
        $cookbook->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cookbook->setTimestamp(time());

            $this->cookbookRepository->create($cookbook);

            return $this->redirectToRoute('cookbook');
        } else {

            /**
             * When the form is not valid it only has recipe weights but not the recipes
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addRecipesService->addRecipesAndValidate($cookbook);
        }

        return $this->render('cookbook/new.html.twig', [
            'cookbook' => $cookbook,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kookboek/verwijder/{id}', name: 'cookbook_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $cookbook = $this->getCookbook($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cookbookRepository->delete($cookbook);
        }

        return $this->redirectToRoute('cookbook');
    }

    #[Route('/kookboek/enkel/{id}', name: 'cookbook_single')]
    public function single(int $id): Response
    {
        $cookbook = $this->getCookbook($id);

        return $this->render('cookbook/single.html.twig', [
            'cookbook' => $cookbook,
        ]);
    }

    private function getCookbook(int $id): Cookbook
    {
        return $this->cookbookRepository->getFromUser($id, $this->getUser()->getId());
    }
}
