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
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends Controller
{
    public function __construct(
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly FormFactoryInterface $formFactory,
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
            'page' => $this->pageRepository->findOneBy(['slug' => 'home']),
            'isFiltered' => $isFiltered,
            'paginator' => $recipes,
            'dietChoices' => Recipe::getDietChoices(true),
            'form' => $form->createView(),
        ]);
    }
}
