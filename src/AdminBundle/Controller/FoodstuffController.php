<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\FoodstuffType;
use App\Controller\AuthController;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class FoodstuffController extends AuthController
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    #[
        Route('/voedingsmiddelen', name: 'admin_foodstuffs', defaults: ['char' => 'A']),
        Route('/voedingsmiddelen/letter/{char}', name: 'admin_foodstuffs_char'),
    ]
    public function view($char = 'A'): Response
    {
        $foodstuffs = $this->foodstuffRepository->findAllStartingWith($char, $this->getUser()->getId());

        return $this->render('@AdminBundle/foodstuff/view.html.twig', [
            'charSelected' => $char,
            'foodstuffs' => $foodstuffs,
        ]);
    }

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'admin_foodstuff_edit')]
    public function edit(Request $request, int $id): Response
    {
        $foodstuff = $this->foodstuffRepository->get($id);

        $isLiquidOld = $foodstuff->isLiquid();

        $form = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $foodstuff, [
            'action' => $this->generateUrl('admin_foodstuff_delete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        /**
         * UniqueEntity of User throws an exception if the User is a proxy. This exception is ignored.
         */
        try {
            $form->handleRequest($request);
        } catch (ConstraintDefinitionException) {
        }

        if ($form->isSubmitted() && $form->isValid()) {

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
                $foodstuff->setUpdatedAt(time());
                $this->foodstuffRepository->update($foodstuff, $isLiquidOld);

                return $this->redirectToRoute('admin_foodstuffs');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/foodstuff/edit.html.twig', [
            'nutrients' => $this->nutrientRepository->findAll(),
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'admin_foodstuff_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $foodstuff = $this->foodstuffRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->foodstuffRepository->delete($foodstuff);
        }

        return $this->redirectToRoute('admin_foodstuffs');
    }
}
