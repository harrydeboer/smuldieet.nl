<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use App\Entity\Cookbook;
use App\Service\AddRecipesService;
use App\UserBundle\Form\CookbookType;
use App\Form\DeleteType;
use App\Repository\CookbookRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
        Route('/kookboeken', name: 'user_cookbooks'),
    ]
    public function view(): Response
    {
        $cookbooks = $this->getUser()->getCookbooks();

        return $this->render('@UserBundle/cookbook/view.html.twig', [
            'cookbooks' => $cookbooks,
            'page' => $this->pageRepository->findOneBy(['title' => 'Kookboeken']),
        ]);
    }

    #[Route('/kookboek/wijzig/{id}', name: 'user_cookbook_edit')]
    public function edit(Request $request, int $id): Response
    {
        $cookbook = $this->getCookbook($id);

        $formUpdate = $this->createForm(CookbookType::class, $cookbook, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $cookbook, [
            'action' => $this->generateUrl('user_cookbook_delete', ['id' => $cookbook->getId()]),
            'method' => 'POST',
        ]);

        $oldRecipeWeights = new ArrayCollection();
        foreach ($cookbook->getRecipeWeights() as $weight) {
            $oldRecipeWeights->add($weight);
        }

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted()
            && $this->addRecipesService->add($cookbook, $this->getUser()->getId())
            && $formUpdate->isValid()) {
            $this->cookbookRepository->update($cookbook, $oldRecipeWeights);

            return $this->redirectToRoute('user_cookbooks');
        }

        return $this->render('@UserBundle/cookbook/edit.html.twig', [
            'cookbook' => $cookbook,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/kookboek/toevoegen', name: 'user_cookbook_create')]
    public function new(Request $request): Response
    {
        $cookbook = new Cookbook();
        $form = $this->createForm(CookbookType::class, $cookbook);
        $cookbook->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $this->addRecipesService->add($cookbook, $this->getUser()->getId())
            && $form->isValid()) {
            $cookbook->setTimestamp(time());

            $this->cookbookRepository->create($cookbook);

            return $this->redirectToRoute('user_cookbooks');
        }

        return $this->render('@UserBundle/cookbook/new.html.twig', [
            'cookbook' => $cookbook,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/kookboek/verwijder/{id}', name: 'user_cookbook_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $cookbook = $this->getCookbook($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cookbookRepository->delete($cookbook);
        }

        return $this->redirectToRoute('user_cookbooks');
    }

    #[Route('/kookboek/enkel/{id}', name: 'user_cookbook_single')]
    public function single(int $id): Response
    {
        $cookbook = $this->getCookbook($id);

        return $this->render('@UserBundle/cookbook/single.html.twig', [
            'cookbook' => $cookbook,
        ]);
    }

    private function getCookbook(int $id): Cookbook
    {
        return $this->cookbookRepository->getFromUser($id, $this->getUser()->getId());
    }
}
