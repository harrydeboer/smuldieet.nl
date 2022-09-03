<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\FoodstuffType;
use App\Controller\AuthController;
use App\Entity\Foodstuff;
use App\Repository\FoodstuffRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class FoodstuffController extends AuthController
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    #[
        Route('/voedingsmiddelen', name: 'adminFoodstuff', defaults: ['char' => 'A']),
        Route('/voedingsmiddelen/letter/{char}', name: 'adminFoodstuffChar'),
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
        $pieceWeightOld = $foodstuff->getPieceWeight();

        $formUpdate = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $foodstuff, [
            'action' => $this->generateUrl('adminFoodstuffDelete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {

            /**
             * Check if a foodstuff of a user with the same name already exists.
             * When the user is null then the foodstuff name must be unique as well.
             */
            if (is_null($foodstuff->getUser())) {
                $foodstuffSameName = $this->foodstuffRepository->findOneBy([
                    'user' => null,
                    'name' => $foodstuff->getName(),
                    ]);
            } else {
                $foodstuffSameName = $this->foodstuffRepository->findOneBy([
                    'user' => $foodstuff->getUser()->getId(),
                    'name' => $foodstuff->getName(),
                    ]);
            }

            try {
                if (!is_null($foodstuffSameName) && $foodstuff->getId() !== $foodstuffSameName->getId()) {
                    throw new Exception('Er is al een voedingsmiddel met deze naam.');
                }
                $this->foodstuffRepository->update($foodstuff, $pieceWeightOld);

                return $this->redirectToRoute('adminFoodstuff');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
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
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->foodstuffRepository->delete($foodstuff);
        }

        return $this->redirectToRoute('adminFoodstuff');
    }
}
