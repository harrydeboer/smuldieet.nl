<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\NutrientType;
use App\Entity\Nutrient;
use App\Controller\AuthController;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NutrientController extends AuthController
{
    public function __construct(
        private readonly NutrientRepositoryInterface $nutrientRepository,
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    #[Route('/voedingsstoffen', name: 'admin_nutrients')]
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
        $oldUnit = $nutrient->getUnit();

        $form = $this->createForm(NutrientType::class, $nutrient, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->validateNutrient($nutrient);

                $this->nutrientRepository->update();

                /**
                 * When the nutrient unit changes the foodstuffs are refactored.
                 */
                $factors = array_merge(
                    Nutrient::ENERGY_UNITS,
                    Nutrient::SOLID_UNITS,
                    Nutrient::LIQUID_UNITS,
                    Nutrient::VITAMIN_MINERAL_UNITS,
                );
                if ($oldUnit !== $nutrient->getUnit()) {
                    $this->foodstuffRepository->transformUnit($oldUnit, $nutrient, $factors);
                }

                return $this->redirectToRoute('admin_nutrients');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/nutrient/edit.html.twig', [
            'nutrient' => $nutrient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws Exception
     */
    private function validateNutrient(Nutrient $nutrient): void
    {
        if ($nutrient->getName() === 'energy'
            && !in_array($nutrient->getUnit(), array_keys(Nutrient::ENERGY_UNITS))) {
            throw new Exception('Energy has no correct unit.');
        } elseif ($nutrient->getName() !== 'energy'
            && in_array($nutrient->getUnit(), array_keys(Nutrient::ENERGY_UNITS))) {
            throw new Exception('Only energy can have this unit.');
        }

        if ($nutrient->getName() !== 'water'
            && $nutrient->getName() !== 'alcohol'
            && in_array($nutrient->getUnit(), array_keys(Nutrient::LIQUID_UNITS))) {
            throw new Exception('Only water and alcohol can have liquid units.');
        }

        if (!is_null($nutrient->getMinRDA())
            && !is_null($nutrient->getMaxRDA())
            && $nutrient->getMinRDA() > $nutrient->getMaxRDA()) {
            throw new Exception('Minimum ADH kan niet groter zijn dan maximum ADH.');
        }
    }
}
