<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\NutrientType;
use App\Entity\Foodstuff;
use App\Entity\FoodstuffWeight;
use App\Entity\Nutrient;
use App\Controller\AuthController;
use App\Repository\NutrientRepositoryInterface;
use ErrorException;
use Symfony\Component\Form\FormError;
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
            'nutrient' => $nutrient,
            'formUpdate' => $formUpdate->createView(),
        ]);
    }

    /**
     * @throws ErrorException
     */
    private function validateNutrient(Nutrient $nutrient): void
    {
        $testFoodstuff = new Foodstuff();

        $property = $testFoodstuff->{'get' . ucfirst($nutrient->getName())}();

        if (is_null($property) && $nutrient->getUnit() === 'stuks') {
            throw new ErrorException('Voedingsstof mag niet eenheid stuks hebben.');
        }

        if (is_null($property) && $nutrient->getName() === 'energyKcal' && $nutrient->getUnit() !== 'kcal') {
            throw new ErrorException('Energie moet in kcal.');
        } elseif (is_null($property) && $nutrient->getName() !== 'energyKcal' && $nutrient->getUnit() === 'kcal') {
            throw new ErrorException('Alleen energie mag in kcal.');
        }

        if (is_null($property) && $nutrient->getName() !== 'water'
            && $nutrient->getName() !== 'alcohol' && in_array($nutrient->getUnit(), FoodstuffWeight::LIQUID_UNITS)) {
            throw new ErrorException('Alleen water en alcohol kunnen vloeibare eenheden hebben.');
        }

        if (!is_null($nutrient->getMinRDA())
            && !is_null($nutrient->getMaxRDA())
            && $nutrient->getMinRDA() > $nutrient->getMaxRDA()) {
            throw new ErrorException('Minimum ADH kan niet groter zijn dan maximum ADH.');
        }
    }
}
