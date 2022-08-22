<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Foodstuff;
use App\Form\FoodstuffFromFoodstuffsType;
use App\Form\FoodstuffType;
use App\Form\DeleteFoodstuffType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\CombineFoodstuffsService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
        Route('/voedingsmiddelen/letter/{char}', name: 'foodstuffChar'),
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

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'foodstuffEdit')]
    public function edit(Request $request, int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);

        $formUpdate = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteFoodstuffType::class, $foodstuff, [
            'action' => $this->generateUrl('foodstuffDelete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid() && $this->checkCanEdit($foodstuff)) {
            try {
                $this->foodstuffRepository->update($foodstuff);

                return $this->redirectToRoute('foodstuff');
            } catch (BadRequestException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/edit/view.html.twig', [
            'foodstuff' => $foodstuff,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/van-voedingsmiddelen', name: 'foodstuffFromFoodstuffsCreate')]
    public function newFromFoodstuffs(Request $request): Response
    {
        $foodstuff = new Foodstuff();
        $form = $this->createForm(FoodstuffFromFoodstuffsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodstuff = $this->combineFoodstuffsService->combine($this->getUser(), $form->getData());
            $foodstuffSameName = $this->foodstuffRepository->findOneBy(['user' => $foodstuff->getUser()->getId(),
                'name' => $foodstuff->getName()]);
            try {
                if (!is_null($foodstuffSameName)) {
                    throw new BadRequestException('Er is al een voedingsmiddel met deze naam.');
                }
                $this->foodstuffRepository->create($foodstuff);

                return $this->redirectToRoute('foodstuff');
            } catch (BadRequestException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/new/fromFoodstuffs.html.twig', [
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'foodstuffDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $foodstuff = $this->getFoodstuff($id);
        $form = $this->createForm(DeleteFoodstuffType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->checkCanEdit($foodstuff)) {
                $this->foodstuffRepository->delete($foodstuff);
            }
        }

        return $this->redirectToRoute('foodstuff');
    }

    #[Route('/voedingsmiddel/enkel/{id}', name: 'foodstuffSingle')]
    public function single(int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);
        if (is_null($foodstuff->getUser()) || $foodstuff->getUser()->getId() === $this->getUser()?->getId()) {
            return $this->render('foodstuff/single/view.html.twig', [
                'foodstuff' => $foodstuff,
                'isLoggedIn' => !is_null($this->getUser()),
            ]);
        }

        throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet of hoort niet bij jou.');
    }

    #[Route('/voedingsmiddel/zoeken/{rowId}/{name}', name: 'foodstuffSearch')]
    public function search(string $rowId, string $name): Response
    {
        if (strlen($name) > 255) {
            $foodstuffs = [];
        } else {
            $foodstuffs = $this->foodstuffRepository->search($this->transformDiacriticChars($name),
                $this->getUser()->getId());
        }

        return $this->render('foodstuff/search.html.twig', [
            'rowId' => $rowId,
            'foodstuffs' => $foodstuffs,
        ]);
    }

    private function getFoodstuff(int $id): Foodstuff
    {
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $this->foodstuffRepository->get($id);
    }

    private function checkCanEdit(Foodstuff $foodstuff): bool
    {
        if ($foodstuff->getUser() === $this->getUser()) {
            return true;
        }

        throw new NotFoundHttpException('Dit voedingsmiddel hoort niet bij jou.');
    }
}
