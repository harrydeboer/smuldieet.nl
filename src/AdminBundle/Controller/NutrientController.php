<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\NutrientType;
use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use App\Form\DeleteType;
use App\Controller\AuthController;
use App\Repository\NutrientRepositoryInterface;
use ErrorException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NutrientController extends AuthController
{
    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
    ) {
    }

    #[Route('/voedingsstoffen', name: 'admin_nutrient')]
    public function view(): Response
    {
        $nutrients = $this->nutrientRepository->findAll();

        return $this->render('@AdminBundle/nutrient/view.html.twig', [
            'nutrients' => $nutrients,
        ]);
    }

    #[Route('/voedingsstof/wijzig/{id}', name: 'admin_nutrient_edit')]
    public function edit(Request $request, int $id): Response
    {
        $nutrient = $this->nutrientRepository->get($id);

        $formUpdate = $this->createForm(NutrientType::class, $nutrient, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $nutrient, [
            'action' => $this->generateUrl('admin_nutrient_delete', ['id' => $nutrient->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                $this->validateNutrient($nutrient);

                $this->nutrientRepository->update();

                return $this->redirectToRoute('admin_nutrient');
            } catch (ErrorException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/nutrient/edit.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsstof/toevoegen', name: 'admin_nutrient_create')]
    public function new(Request $request): Response
    {
        $nutrient = new Nutrient();
        $form = $this->createForm(NutrientType::class, $nutrient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $this->validateNutrient($nutrient);

                $this->nutrientRepository->create($nutrient);

                return $this->redirectToRoute('admin_nutrient');
            } catch (ErrorException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/nutrient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsstof/verwijder/{id}', name: 'admin_nutrient_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $nutrient = $this->nutrientRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->nutrientRepository->delete($nutrient);
        }

        return $this->redirectToRoute('admin_nutrient');
    }

    /**
     * @throws ErrorException
     */
    private function validateNutrient(Nutrient $nutrient): void
    {
        $testFoodstuff = new Foodstuff();

        $property = $testFoodstuff->{'get' . ucfirst($nutrient->getName())}();

        if (is_null($property) && $nutrient->getName() !== 'water'
            && $nutrient->getName() !== 'alcohol' && in_array($nutrient->getUnit(), FoodstuffWeight::LIQUID_UNITS)) {
            throw new ErrorException('Alleen water en alcohol kunnen vloeibare eenheden hebben.');
        }
    }
}
