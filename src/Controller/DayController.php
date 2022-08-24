<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Day;
use App\Form\DayType;
use App\Form\DeleteType;
use App\Form\StandardDayType;
use App\Repository\DayRepositoryInterface;
use App\Repository\PageRepositoryInterface;
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
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[
        Route('/dagboek', name: 'diary', defaults: ['page' => '1']),
        Route('/dagboek/pagina/{page<[1-9]\d*>}', name: 'diaryIndexPaginated'),
    ]
    public function view(int $page): Response
    {
        $days = $this->dayRepository->findFromUserSorted($this->getUser()->getId(), $page);
        $dayStandard = $this->dayRepository->findOneBy(['user' => $this->getUser()->getId(), 'timestamp' => null]);

        return $this->render('day/view.html.twig', [
            'paginator' => $days,
            'standardDay' => $dayStandard,
            'page' => $this->pageRepository->findOneBy(['title' => 'Dagboek']),
        ]);
    }

    #[Route('/dag/wijzig/{id}', name: 'dayEdit')]
    public function edit(Request $request, int $id): Response
    {
        $day = $this->getDay($id);
        $dayStandard = $this->dayRepository->findOneBy(['user' => $this->getUser()->getId(), 'timestamp' => null]);

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
            'action' => $this->generateUrl('dayDelete', ['id' => $day->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            if ($dayStandard?->getId() !== $day->getId() && is_null($day->getTimestamp())) {
                throw new BadRequestException('The day cannot become the standard day.');
            }

            $this->dayRepository->update($day);

            return $this->redirectToRoute('diary');
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
            if (is_null($day->getTimestamp())) {
                throw new BadRequestException('The day must have a date.');
            }

            $this->dayRepository->create($day);

            return $this->redirectToRoute('diary');
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

            if (!is_null($dayStandard)) {
                throw new BadRequestException('There can only be one standard day.');
            }

            $this->dayRepository->create($day);

            return $this->redirectToRoute('diary');
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

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->dayRepository->delete($day);
        }

        return $this->redirectToRoute('diary');
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
