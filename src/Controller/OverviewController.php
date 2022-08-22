<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\OverviewType;
use App\Repository\DayRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\StatsService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AuthController
{
    public function __construct(
        private readonly DayRepositoryInterface $dayRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly StatsService $statsService,
    ) {
    }

    #[
        Route('/overzicht', name: 'overview'),
    ]
    public function view(Request $request): Response
    {
        $form = $this->formFactory->createNamed('', OverviewType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);
        $days = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $this->dayRepository->findBetween($form->get('start')->getData(),
                $form->get('end')->getData(), $this->getUser()->getId());
        }

        return $this->render('overview/view.html.twig', [
            'stats' => $this->statsService->daysStats($days, $this->getUser()),
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Overzicht']),
        ]);
    }
}
