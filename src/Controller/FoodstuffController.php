<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Foodstuff;
use App\Entity\NutrientsInterface;
use App\Form\CombineFoodstuffsType;
use App\Form\FoodstuffType;
use App\Form\DeleteType;
use App\Repository\FoodstuffRepositoryInterface;
use App\Repository\NutrientRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

class FoodstuffController extends Controller
{
    public function __construct(
        private readonly FoodstuffRepositoryInterface $foodstuffRepository,
        private readonly NutrientRepositoryInterface $nutrientRepository,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[
        Route('/voedingsmiddelen', name: 'foodstuff', defaults: ['char' => 'A']),
        Route('/voedingsmiddelen/letter/{char}', name: 'foodstuff_char'),
    ]
    public function view(Request $request, string $char = 'A'): Response
    {
        if ($request->attributes->get('_route') === 'foodstuff_char' && $char === 'A') {
            return $this->redirectToRoute('foodstuff');
        }

        $foodstuffs = $this->foodstuffRepository->findAllStartingWith($char, $this->getUser()?->getId());

        return $this->render('foodstuff/view.html.twig', [
            'charSelected' => $char,
            'foodstuffs' => $foodstuffs,
            'currentUser' => $this->getUser(),
            'page' => $this->pageRepository->findOneBy(['slug' => 'voedingsmiddelen']),
        ]);
    }

    #[Route('/voedingsmiddel/wijzig/{id}', name: 'foodstuff_edit')]
    public function edit(Request $request, int $id): Response
    {
        $foodstuff = $this->getFoodstuff($id);

        /**
         * When the foodstuff updates it is checked if the foodstuff is set to solid.
         * The units of the foodstuff weights are then set to solid units.
         */
        $isLiquidOld = $foodstuff->isLiquid();

        $form = $this->createForm(FoodstuffType::class, $foodstuff, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $foodstuff, [
            'action' => $this->generateUrl('foodstuff_delete', ['id' => $foodstuff->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->checkCanEdit($foodstuff)) {
            try {
                $foodstuff->setUpdatedAt(time());
                $this->foodstuffRepository->update($foodstuff, $isLiquidOld);

                return $this->redirectToRoute('foodstuff');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/edit.html.twig', [
            'nutrients' => $this->nutrientRepository->findAll(),
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/combineer-voedingsmiddelen', name: 'combine_foodstuffs')]
    public function combineFoodstuffs(Request $request): Response
    {
        $foodstuff = new Foodstuff();
        $form = $this->createForm(CombineFoodstuffsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $foodstuffWeights = new ArrayCollection($form->get('foodstuff_weights')->getData());
        } else {
            $foodstuffWeights = new ArrayCollection();
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $foodstuff = new Foodstuff();
            $foodstuff->setName($form->get('name')->getData());
            $foodstuff->setUser($this->getUser());

            $foodstuffSameName = $this->foodstuffRepository->findOneBy([
                'user' => $foodstuff->getUser()->getId(),
                'name' => $foodstuff->getName(),
            ]);

            $totalWeight = 0;
            foreach ($foodstuffWeights as $weight) {
                $totalWeight += $weight->getValue();
            }

            /**
             * The foodstuff gets the weighed values from the foodstuffs of the form.
             */
            foreach ($foodstuffWeights as $weight) {
                $foodstuffWeight = $weight->getFoodstuff();
                foreach (NutrientsInterface::NAMES as $name) {
                    if (is_null($foodstuffWeight->{'get' . ucfirst($name)}())) {
                        continue;
                    } else {
                        $foodstuff->{'set' . ucfirst($name)}($foodstuff->{'get' . ucfirst($name)}() +
                            $foodstuffWeight->{'get' . ucfirst($name)}()
                            * $weight->getValue() / $totalWeight);
                    }
                }
            }

            try {
                if ((int)round(($totalWeight * 100)) !== 10000) {
                    throw new Exception('Gewichten moeten samen 100 procent zijn.');
                }

                if (!is_null($foodstuffSameName)) {
                    throw new Exception('Er is al een voedingsmiddel met deze naam.');
                }
                $foodstuff->setCreatedAt(time());

                $this->foodstuffRepository->create($foodstuff);

                $this->addFlash('combineFoodstuffs', 'Nu het voedingsmiddel gemaakt is uit andere 
                    voedingsmiddelen kun je nog de voedingswaarden op het etiket invullen voor jouw voedingsmiddel.');

                return $this->redirectToRoute('foodstuff_edit', ['id' => $foodstuff->getId()]);
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('foodstuff/combine_foodstuffs.html.twig', [
            'foodstuffWeights' => $foodstuffWeights,
            'foodstuff' => $foodstuff,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voedingsmiddel/verwijder/{id}', name: 'foodstuff_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $foodstuff = $this->getFoodstuff($id);
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->checkCanEdit($foodstuff)) {
                $this->foodstuffRepository->delete($foodstuff);
            }
        }

        return $this->redirectToRoute('foodstuff');
    }

    #[Route('/voedingsmiddel/enkel/{id}', name: 'foodstuff_single')]
    public function single(int $id): Response
    {
        $foodstuff = $this->foodstuffRepository->getDefaultAndFromUser($id, $this->getUser()?->getId());
        if (is_null($foodstuff->getUser()) || $foodstuff->getUser()->getId() === $this->getUser()?->getId()) {
            return $this->render('foodstuff/single.html.twig', [
                'foodstuff' => $foodstuff,
                'nutrients' => $this->nutrientRepository->findAll(),
                'isLoggedIn' => !is_null($this->getUser()),
            ]);
        }

        throw $this->createNotFoundException('Dit voedingsmiddel bestaat niet of hoort niet bij jou.');
    }

    #[Route('/voedingsmiddel/zoeken/{name}', name: 'foodstuff_search')]
    public function search(string $name = ''): Response
    {
        if (strlen($name) > 255) {
            $foodstuffs = [];
        } else {
            $foodstuffs = $this->foodstuffRepository->search($name, $this->getUser()->getId(),
            );
        }

        return $this->render('foodstuff/search.html.twig', [
            'foodstuffs' => $foodstuffs,
        ]);
    }

    private function getFoodstuff(int $id): Foodstuff
    {
        return $this->foodstuffRepository->getFromUser($id, $this->getUser()->getId());
    }

    private function checkCanEdit(Foodstuff $foodstuff): bool
    {
        if ($foodstuff->getUser() === $this->getUser()) {
            return true;
        }

        throw $this->createNotFoundException('Dit voedingsmiddel hoort niet bij jou.');
    }
}
