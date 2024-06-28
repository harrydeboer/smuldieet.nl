<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\StartAndEndDateType;
use App\Repository\DayRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Service\RDAService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticsController extends AuthController
{
    public function __construct(
        private readonly DayRepositoryInterface  $dayRepository,
        private readonly FormFactoryInterface    $formFactory,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly RDAService              $RDAService,
    ) {
    }

    #[
        Route('/statistieken', name: 'statistics'),
    ]
    public function view(Request $request): Response
    {
        $form = $this->formFactory->createNamed('', StartAndEndDateType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);
        $days = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $days = $this->dayRepository->findBetween($form->get('start')->getData(),
                $form->get('end')->getData(), $this->getUser()->getId());
            $isSubmitted = true;
        } else {
            $isSubmitted = false;
        }

        return $this->render('overview/view.html.twig', [
            'nutrients' => $this->RDAService->daysStats($days, $this->getUser()),
            'form' => $form->createView(),
            'isSubmitted' => $isSubmitted,
            'page' => $this->pageRepository->findOneBy(['slug' => 'statistieken']),
        ]);
    }
}
