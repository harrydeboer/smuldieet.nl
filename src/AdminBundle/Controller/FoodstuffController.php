<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\DeleteFoodstuffType;
use App\AdminBundle\Form\FoodstuffType;
use App\Controller\AuthController;
use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FoodstuffController extends AuthController
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    #[
        Route('/voedingsmiddel', name: 'adminFoodstuff', defaults: ['char' => 'A']),
        Route('/voedingsmiddel/letter/{char}', name: 'adminFoodstuffChar'),
    ]
    public function view($char = 'A'): Response
    {
        $foodstuffs = $this->foodstuffRepository->findAllStartingWith($char, $this->getUser()->getId());

        return $this->render('@AdminBundle/foodstuff/view.html.twig', [
            'charSelected' => $char,
            'foodstuffs' => $foodstuffs,
        ]);
    }

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'adminFoodstuffEdit')]
    public function edit(Request $request, Foodstuff $foodstuff): Response
    {
        $formUpdate = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteFoodstuffType::class, $foodstuff, [
            'action' => $this->generateUrl('adminFoodstuffDelete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            if (is_null($error = $this->foodstuffRepository->update($foodstuff))) {
                return $this->redirectToRoute('adminFoodstuff');
            }

            $formUpdate->addError(new FormError($error));
        }

        return $this->render('@AdminBundle/foodstuff/edit/view.html.twig', [
            'foodstuff' => $foodstuff,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'adminFoodstuffDelete')]
    public function delete(Request $request, Foodstuff $foodstuff): RedirectResponse
    {
        $form = $this->createForm(DeleteFoodstuffType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->foodstuffRepository->delete($foodstuff);
        }

        return $this->redirectToRoute('adminFoodstuff');
    }
}
