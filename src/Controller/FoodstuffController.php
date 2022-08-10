<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Foodstuff;
use App\Form\FoodstuffFromFoodstuffsType;
use App\Form\FoodstuffType;
use App\Form\DeleteFoodstuffType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Service\CombineFoodstuffsService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FoodstuffController extends Controller
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
    ) {
    }

    #[
        Route('/voedingsmiddel', name: 'foodstuff', defaults: ['char' => 'A']),
        Route('/voedingsmiddel/letter/{char}', name: 'foodstuffChar'),
    ]
    public function view(string $char = 'A'): Response
    {
        $foodstuffs = $this->foodstuffRepository->findAllStartingWith($char, $this->getUser()?->getId());

        return $this->render('foodstuff/view.html.twig', [
            'charSelected' => $char,
            'foodstuffs' => $foodstuffs,
        ]);
    }

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'foodstuffEdit')]
    public function edit(Request $request, int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);

        $formUpdate = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteFoodstuffType::class, $foodstuff, [
            'action' => $this->generateUrl('foodstuffDelete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            if (is_null($message = $this->checkWeightsAndEnergy($foodstuff))) {
                if ($this->checkCanEdit($foodstuff)) {
                    $this->foodstuffRepository->update();
                }

                return $this->redirectToRoute('foodstuff');
            } else {
                $formUpdate->addError(new FormError($message));
            }
        }

        return $this->render('foodstuff/edit/view.html.twig', [
            'foodstuff' => $foodstuff,
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/toevoegen', name: 'foodstuffCreate')]
    public function new(Request $request): Response
    {
        $foodstuff = new Foodstuff();
        $form = $this->createForm(FoodstuffType::class, $foodstuff);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (is_null($message = $this->checkWeightsAndEnergy($foodstuff))) {
                $foodstuff->setUser($this->getUser());

                $this->foodstuffRepository->create($foodstuff);

                return $this->redirectToRoute('foodstuff');
            } else {
                $form->addError(new FormError($message));
            }
        }

        return $this->render('foodstuff/new/view.html.twig', [
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/van-voedingsmiddelen', name: 'foodstuffFromFoodstuffsCreate')]
    public function newFromFoodstuffs(Request $request): Response
    {
        $foodstuff = new Foodstuff();
        $form = $this->createForm(FoodstuffFromFoodstuffsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $foodstuff = CombineFoodstuffsService::combine($form->getData(), $this->getUser());
            if (is_null($message = $this->checkWeightsAndEnergy($foodstuff))) {
                $foodstuff->setUser($this->getUser());
                $this->foodstuffRepository->create($foodstuff);

                return $this->redirectToRoute('foodstuff');

            } else {
                $form->addError(new FormError($message));
            }
        }

        return $this->render('foodstuff/new/fromFoodstuffs.html.twig', [
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'foodstuffDelete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $foodstuff = $this->getFoodstuff($id);
        $form = $this->createForm(DeleteFoodstuffType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->checkCanEdit($foodstuff)) {
                $this->foodstuffRepository->delete($foodstuff);
            }
        }

        return $this->redirectToRoute('foodstuff');
    }

    #[Route('/voedingsmiddel/enkel/{id}', name: 'foodstuffSingle')]
    public function single(int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);
        if (is_null($foodstuff->getUser()) || $foodstuff->getUser()->getId() === $this->getUser()?->getId()) {
            return $this->render('foodstuff/single/view.html.twig', [
                'foodstuff' => $foodstuff,
                'isLoggedIn' => !is_null($this->getUser()),
            ]);
        }

        throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet of hoort niet bij jou.');
    }

    private function checkWeightsAndEnergy(Foodstuff $foodstuff): ?string
    {
        $sum = 0;
        foreach (Foodstuff::getADH() as $key => $property) {
            if ($key === 'energyKcal' || $key === 'saturatedFat' || $key === 'monounsaturatedFat'
                || $key === 'polyunsaturatedFat'|| $key === 'sucre') {
                continue;
            }
            $factor = 1;
            if ($property[2] === 'mg') {
                $factor = 0.001;
            } elseif ($property[2] === 'μg') {
                $factor = 0.000001;
            }
            $sum = $sum + $foodstuff->{'get' . ucfirst($key)}() * $factor;
        }

        if ($sum < 85 || $sum > 115) {
            return 'De gewichten van het voedingsmiddel moeten samen gelijk aan 100g zijn.';
        }

        if ($foodstuff->getSucre() > $foodstuff->getCarbohydrates()) {
            return 'Suiker mag niet zwaarder zijn dan koolhydraten.';
        }

        $energy = $foodstuff->getCarbohydrates() * 4 + $foodstuff->getProtein() * 4 +
            $foodstuff->getFat() * 9 + $foodstuff->getAlcohol() * 7 + $foodstuff->getDietaryFiber() * 2;
        $allowed = $energy * 0.12;
        if (abs($foodstuff->getEnergyKcal() - $energy) > $allowed) {
            return 'De totale energy klopt niet met de energieën uit koolhydraten, eiwit, vet, alcohol  en vezels.';
        }

        return null;
    }

    private function getFoodstuff(int $id): Foodstuff
    {
        if ($id > 2147483647) {
            throw new NotFoundHttpException('Dit voedingsmiddel bestaat niet.');
        }

        return $this->foodstuffRepository->get($id);
    }

    private function checkCanEdit(Foodstuff $foodstuff): bool
    {
        if ($foodstuff->getUser() === $this->getUser()) {
            return true;
        }

        throw new NotFoundHttpException('Dit voedingsmiddel hoort niet bij jou.');
    }
}
