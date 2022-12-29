<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Foodstuff;
use App\Form\FoodstuffFromFoodstuffsType;
use App\Form\FoodstuffType;
use App\Form\DeleteType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\CombineFoodstuffsService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class FoodstuffController extends Controller
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly CombineFoodstuffsService $combineFoodstuffsService,
    ) {
    }

    #[
        Route('/voedingsmiddelen', name: 'foodstuff', defaults: ['char' => 'A']),
        Route('/voedingsmiddelen/letter/{char}', name: 'foodstuff_char'),
    ]
    public function view(string $char = 'A'): Response
    {
        $foodstuffs = $this->foodstuffRepository->findAllStartingWith($char, $this->getUser()?->getId());

        return $this->render('foodstuff/view.html.twig', [
            'charSelected' => $char,
            'foodstuffs' => $foodstuffs,
            'currentUser' => $this->getUser(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Voedingsmiddelen']),
        ]);
    }

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'foodstuff_edit')]
    public function edit(Request $request, int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);
        $isLiquidOld = $foodstuff->getIsLiquid();

        $formUpdate = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $foodstuff, [
            'action' => $this->generateUrl('foodstuff_delete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid() && $this->checkCanEdit($foodstuff)) {
            try {
                $this->foodstuffRepository->update($foodstuff, $isLiquidOld);

                return $this->redirectToRoute('foodstuff');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/edit.html.twig', [
            'foodstuff' => $foodstuff,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/van-voedingsmiddelen', name: 'foodstuff_from_foodstuffs_create')]
    public function newFromFoodstuffs(Request $request): Response
    {
        $foodstuff = new Foodstuff();
        $form = $this->createForm(FoodstuffFromFoodstuffsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodstuff = $this->combineFoodstuffsService->combine($this->getUser(), $form->getData());
            $foodstuffSameName = $this->foodstuffRepository->findOneBy([
                'user' => $foodstuff->getUser()->getId(),
                'name' => $foodstuff->getName(),
            ]);
            try {
                if (!is_null($foodstuffSameName)) {
                    throw new Exception('Er is al een voedingsmiddel met deze naam.');
                }
                $this->foodstuffRepository->create($foodstuff);

                return $this->redirectToRoute('foodstuff_edit', ['id' => $foodstuff->getId()]);
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/foodstuff_from_foodstuffs.html.twig', [
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'foodstuff_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $foodstuff = $this->getFoodstuff($id);
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->checkCanEdit($foodstuff)) {
                $this->foodstuffRepository->delete($foodstuff);
            }
        }

        return $this->redirectToRoute('foodstuff');
    }

    #[Route('/voedingsmiddel/enkel/{id}', name: 'foodstuff_single')]
    public function single(int $id): Response
    {
        $foodstuff = $this->foodstuffRepository->get($id, $this->getUser()?->getId());
        if (is_null($foodstuff->getUser()) || $foodstuff->getUser()->getId() === $this->getUser()?->getId()) {
            return $this->render('foodstuff/single.html.twig', [
                'foodstuff' => $foodstuff,
                'isLoggedIn' => !is_null($this->getUser()),
            ]);
        }

        throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet of hoort niet bij jou.');
    }

    #[Route('/voedingsmiddel/zoeken/{name}', name: 'foodstuff_search')]
    public function search(string $name = ''): Response
    {
        if (strlen($name) > 255) {
            $foodstuffs = [];
        } else {
            $foodstuffs = $this->foodstuffRepository->search(
                $this->transformDiacriticChars($name),
                $this->getUser()->getId(),
            );
        }

        return $this->render('foodstuff/search.html.twig', [
            'foodstuffs' => $foodstuffs,
        ]);
    }

    private function getFoodstuff(int $id): Foodstuff
    {
        return $this->foodstuffRepository->getFromUser($id, $this->getUser()->getId());
    }

    private function checkCanEdit(Foodstuff $foodstuff): bool
    {
        if ($foodstuff->getUser() === $this->getUser()) {
            return true;
        }

        throw new NotFoundHttpException('Dit voedingsmiddel hoort niet bij jou.');
    }
}
