<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeFilterAndSortType;
use App\Repository\PageRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends Controller
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly KernelInterface $kernel,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[
        Route('/', name: 'homepage', defaults: ['page' => '1']),
        Route('/pagina/{page<[1-9]\d*>}', name: 'homepage_index_paginated'),
    ]
    public function view(Request $request, int $page): Response
    {
        $form = $this->formFactory->createNamed('', RecipeFilterAndSortType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isFiltered = true;
            $recipes = $this->recipeRepository->findBySortAndFilter($page, $form->getData());
        } else {
            $isFiltered = false;
            $recipes = $this->recipeRepository->findRecent(5);
        }

        return $this->render('homepage/view.html.twig', [
            'page' => $this->pageRepository->findOneBy(['title' => 'Home']),
            'isFiltered' => $isFiltered,
            'paginator' => $recipes,
            'dietChoices' => Recipe::DIET_CHOICES,
            'appEnv' => $this->kernel->getEnvironment(),
            'form' => $form->createView(),
        ]);
    }
}
