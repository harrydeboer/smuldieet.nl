<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayType;
use App\Form\DeleteType;
use App\Form\StandardDayType;
use App\Repository\DayRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\AddFoodstuffsService;
use App\Service\AddRecipesService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DayController extends AuthController
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly AddRecipesService $addRecipesService,
        private readonly AddFoodstuffsService $addFoodstuffsService,
    ) {
    }

    #[
        Route('/dagboek', name: 'diary', defaults: ['page' => '1']),
        Route('/dagboek/pagina/{page<[1-9]\d*>}', name: 'diary_index_paginated'),
    ]
    public function view(int $page): Response
    {
        $days = $this->dayRepository->findFromUserSorted($this->getUser()->getId(), $page);
        $dayStandard = $this->dayRepository->findOneBy([
            'user' => $this->getUser()->getId(),
            'timestamp' => null,
        ]);

        return $this->render('day/view.html.twig', [
            'paginator' => $days,
            'standardDay' => $dayStandard,
            'page' => $this->pageRepository->findOneBy(['title' => 'Dagboek']),
        ]);
    }

    #[Route('/dag/wijzig/{id}', name: 'day_edit')]
    public function edit(Request $request, int $id): Response
    {
        $day = $this->getDay($id);
        $dayStandard = $this->dayRepository->findOneBy([
            'user' => $this->getUser()->getId(),
            'timestamp' => null,
        ]);

        if (is_null($day->getTimestamp())) {
            $formUpdate = $this->createForm(StandardDayType::class, $day, [
                'method' => 'POST',
            ]);
        } else {
            $formUpdate = $this->createForm(DayType::class, $day, [
                'method' => 'POST',
            ]);
        }

        $formDelete = $this->createForm(DeleteType::class, $day, [
            'action' => $this->generateUrl('day_delete', ['id' => $day->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            if ($dayStandard?->getId() !== $day->getId() && is_null($day->getTimestamp())) {
                throw new BadRequestException('The day cannot become the standard day.');
            }

            $this->dayRepository->update($day);

            return $this->redirectToRoute('diary');
        } else {

            /**
             * When the form is not valid it only has recipe and foodstuff weights but not the recipes and foodstuffs
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addRecipesService->addRecipesAndValidate($day);
            $this->addFoodstuffsService->addFoodstuffsAndValidate($day);
        }

        return $this->render('day/edit.html.twig', [
            'day' => $day,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/dag/toevoegen', name: 'day_create')]
    public function new(Request $request): Response
    {
        $day = new Day();
        $dayStandard = $this->dayRepository->findOneBy([
            'user' => $this->getUser()->getId(),
            'timestamp' => null,
        ]);
        $form = $this->createForm(DayType::class, $dayStandard ?? $day);
        $day->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($day->getTimestamp())) {
                throw new BadRequestException('The day must have a date.');
            }

            $this->dayRepository->create($day);

            return $this->redirectToRoute('diary');
        } else {

            /**
             * When the form is not valid it only has recipe and foodstuff weights but not the recipes and foodstuffs
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addRecipesService->addRecipesAndValidate($day);
            $this->addFoodstuffsService->addFoodstuffsAndValidate($day);
        }

        return $this->render('day/new.html.twig', [
            'day' => $dayStandard ?? $day,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dag/toevoegen/standaard', name: 'day_create_standard')]
    public function newStandard(Request $request): Response
    {
        $day = new Day();
        $form = $this->createForm(StandardDayType::class, $day);
        $day->setUser($this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dayStandard = $this->dayRepository->findOneBy([
                'user' => $this->getUser()->getId(),
                'timestamp' => null,
            ]);

            if (!is_null($dayStandard)) {
                throw new BadRequestException('There can only be one standard day.');
            }

            $this->dayRepository->create($day);

            return $this->redirectToRoute('diary');
        } else {

            /**
             * When the form is not valid it only has recipe and foodstuff weights but not the recipes and foodstuffs
             * themselves. These are added in order to fill in the names in the form.
             */
            $this->addRecipesService->addRecipesAndValidate($day);
            $this->addFoodstuffsService->addFoodstuffsAndValidate($day);
        }

        return $this->render('day/new_standard_day.html.twig', [
            'day' => $day,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dag/verwijder/{id}', name: 'day_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $day = $this->getDay($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dayRepository->delete($day);
        }

        return $this->redirectToRoute('diary');
    }

    #[Route('/dag/enkel/{id}', name: 'day_single')]
    public function single(int $id): Response
    {
        $day = $this->getDay($id);

        return $this->render('day/single.html.twig', [
            'day' => $day,
        ]);
    }

    private function getDay(int $id): Day
    {
        return $this->dayRepository->getFromUser($id, $this->getUser()->getId());
    }
}
