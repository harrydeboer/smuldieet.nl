<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayType;
use App\Form\DeleteDayType;
use App\Form\StandardDayType;
use App\Repository\DayRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AuthController
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
    ) {
    }

    #[
        Route('/dag', name: 'day', defaults: ['page' => '1']),
        Route('/dag/pagina/{page<[1-9]\d*>}', name: 'dayIndexPaginated'),
    ]
    public function view(int $page): Response
    {
        $days = $this->dayRepository->findFromUserSorted($this->getUser()->getId(), $page);
        $dayStandard = $this->dayRepository->findOneBy(['user' => $this->getUser()->getId(), 'timestamp' => null]);

        return $this->render('day/view.html.twig', [
            'paginator' => $days,
            'standardDay' => $dayStandard,
        ]);
    }

    #[Route('/dag/wijzig/{id}', name: 'dayEdit')]
    public function edit(Request $request, int $id): Response
    {
        $day = $this->getDay($id);

        if (is_null($day->getTimestamp())) {
            $formUpdate = $this->createForm(StandardDayType::class, $day, [
                'method' => 'POST',
            ]);
        } else {
            $formUpdate = $this->createForm(DayType::class, $day, [
                'method' => 'POST',
            ]);
        }

        $formDelete = $this->createForm(DeleteDayType::class, $day, [
            'action' => $this->generateUrl('dayDelete', ['id' => $day->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                foreach ($day->getRecipeIds() as $id) {
                    $recipe = $this->recipeRepository->get($id);
                    $day->addRecipe($recipe);
                }
                $this->dayRepository->update($day);

                return $this->redirectToRoute('day');
            } catch (BadRequestException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('day/edit/view.html.twig', [
            'day' => $day,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/dag/toevoegen', name: 'dayCreate')]
    public function new(Request $request): Response
    {
        $day = new Day();
        $form = $this->createForm(DayType::class, $day);
        $day->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (is_null($day->getTimestamp())) {
                    throw new BadRequestException('De dag moet een datum hebben.');
                }
                foreach ($day->getRecipeIds() as $id) {
                    $recipe = $this->recipeRepository->get($id);
                    $day->addRecipe($recipe);
                }
                $this->dayRepository->create($day);

                return $this->redirectToRoute('day');
            } catch (BadRequestException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        $dayStandard = $this->dayRepository->findOneBy(['user' => $this->getUser()->getId(), 'timestamp' => null]);

        return $this->render('day/new/view.html.twig', [
            'day' => $dayStandard ?? $day,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dag/toevoegen/standaard', name: 'dayCreateStandard')]
    public function newStandard(Request $request): Response
    {
        $day = new Day();
        $form = $this->createForm(StandardDayType::class, $day);
        $day->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dayStandard = $this->dayRepository->findOneBy(['user' => $this->getUser()->getId(), 'timestamp' => null]);
            try {
                if (!is_null($dayStandard)) {
                    throw new BadRequestException('Er kan maar 1 standaard dag zijn.');
                }
                foreach ($day->getRecipeIds() as $id) {
                    $recipe = $this->recipeRepository->get($id);
                    $day->addRecipe($recipe);
                }
                $this->dayRepository->create($day);

                return $this->redirectToRoute('day');
            } catch (BadRequestException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('day/new/standardDay.html.twig', [
            'day' => $day,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dag/verwijder/{id}', name: 'dayDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $day = $this->getDay($id);

        $form = $this->createForm(DeleteDayType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dayRepository->delete($day);
        }

        return $this->redirectToRoute('day');
    }

    #[Route('/dag/enkel/{id}', name: 'daySingle')]
    public function single(int $id): Response
    {
        $day = $this->getDay($id);

        return $this->render('day/single/view.html.twig', [
            'day' => $day,
        ]);
    }

    private function getDay(int $id): Day
    {
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Deze dag bestaat niet.');
        }

        return $this->dayRepository->getFromUser($id, $this->getUser()->getId());
    }
}
