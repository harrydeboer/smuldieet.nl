<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Cookbook;
use App\Form\CookbookType;
use App\Form\DeleteCookbookType;
use App\Repository\CookbookRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CookbookController extends AuthController
{
    public function __construct(
        private readonly CookbookRepositoryInterface $cookbookRepository,
    ) {
    }

    #[
        Route('/kookboek', name: 'cookbook'),
    ]
    public function view(): Response
    {
        $cookbooks = $this->getUser()->getCookbooks();

        return $this->render('cookbook/view.html.twig', [
            'cookbooks' => $cookbooks,
        ]);
    }

    #[Route('/kookboek/wijzig/{id}', name: 'cookbookEdit')]
    public function edit(Request $request, int $id): Response
    {
        $cookbook = $this->getCookbook($id);
        $recipesOld = $cookbook->getRecipes()->toArray();

        $formUpdate = $this->createForm(CookbookType::class, $cookbook, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteCookbookType::class, $cookbook, [
            'action' => $this->generateUrl('cookbookDelete', ['id' => $cookbook->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->cookbookRepository->update($cookbook, $recipesOld);

            return $this->redirectToRoute('cookbook');
        }

        return $this->render('cookbook/edit/view.html.twig', [
            'cookbook' => $cookbook,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/kookboek/toevoegen', name: 'cookbookCreate')]
    public function new(Request $request): Response
    {
        $cookbook = new Cookbook();
        $form = $this->createForm(CookbookType::class, $cookbook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cookbook->setTimestamp(time());
            $cookbook->setUser($this->getUser());
            $this->cookbookRepository->create($cookbook);

            return $this->redirectToRoute('cookbook');
        }

        return $this->render('cookbook/new/view.html.twig', [
            'cookbook' => $cookbook,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kookboek/verwijder/{id}', name: 'cookbookDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $cookbook = $this->getCookbook($id);

        $form = $this->createForm(DeleteCookbookType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cookbookRepository->delete($cookbook);
        }

        return $this->redirectToRoute('cookbook');
    }

    #[Route('/kookboek/enkel/{id}', name: 'cookbookSingle')]
    public function single(int $id): Response
    {
        $cookbook = $this->getCookbook($id);

        return $this->render('cookbook/single/view.html.twig', [
            'cookbook' => $cookbook,
        ]);
    }

    private function getCookbook(int $id): Cookbook
    {
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Dit kookboek bestaat niet.');
        }

        return $this->cookbookRepository->getFromUser($id, $this->getUser()->getId());
    }
}
