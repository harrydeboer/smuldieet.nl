<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Form\DeleteType;
use App\AdminBundle\Form\ProfanityType;
use App\Controller\AuthController;
use App\Entity\Profanity;
use App\Repository\ProfanityRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfanityController extends AuthController
{
    public function __construct(
        private readonly ProfanityRepositoryInterface $profanityRepository,
    ) {
    }

    #[Route('/scheldwoorden', name: 'adminProfanity')]
    public function view(): Response
    {
        $profanities = $this->profanityRepository->findAll();

        return $this->render('@AdminBundle/profanity/view.html.twig', [
            'profanities' => $profanities,
        ]);
    }

    #[Route('/scheldwoord/wijzig/{id}', name: 'adminProfanityEdit')]
    public function edit(Request $request, Profanity $profanity): Response
    {
        $formUpdate = $this->createForm(ProfanityType::class, $profanity, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $profanity, [
            'action' => $this->generateUrl('adminProfanityDelete', ['id' => $profanity->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->profanityRepository->update();

            return $this->redirectToRoute('adminProfanity');
        }

        return $this->render('@AdminBundle/profanity/edit/view.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/scheldwoord/toevoegen', name: 'adminProfanityCreate')]
    public function new(Request $request): Response
    {
        $profanity = new Profanity();
        $form = $this->createForm(ProfanityType::class, $profanity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profanityRepository->create($profanity);

            return $this->redirectToRoute('adminProfanity');
        }

        return $this->render('@AdminBundle/profanity/new/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/scheldwoord/verwijder/{id}', name: 'adminProfanityDelete')]
    public function delete(Request $request, Profanity $profanity): RedirectResponse
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profanityRepository->delete($profanity);
        }

        return $this->redirectToRoute('adminProfanity');
    }
}
