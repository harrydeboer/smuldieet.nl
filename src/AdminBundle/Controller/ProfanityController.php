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
use Symfony\Component\Routing\Attribute\Route;

class ProfanityController extends AuthController
{
    public function __construct(
        private readonly ProfanityRepositoryInterface $profanityRepository,
    ) {
    }

    #[Route('/scheldwoorden', name: 'admin_profanities')]
    public function view(): Response
    {
        $profanities = $this->profanityRepository->findAll();

        return $this->render('@AdminBundle/profanity/view.html.twig', [
            'profanities' => $profanities,
        ]);
    }

    #[Route('/scheldwoord/wijzig/{id}', name: 'admin_profanity_edit')]
    public function edit(Request $request, int $id): Response
    {
        $profanity = $this->profanityRepository->get($id);

        $form = $this->createForm(ProfanityType::class, $profanity, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $profanity, [
            'action' => $this->generateUrl('admin_profanity_delete', ['id' => $profanity->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profanityRepository->update();

            return $this->redirectToRoute('admin_profanities');
        }

        return $this->render('@AdminBundle/profanity/edit.html.twig', [
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/scheldwoord/toevoegen', name: 'admin_profanity_create')]
    public function new(Request $request): Response
    {
        $profanity = new Profanity();
        $form = $this->createForm(ProfanityType::class, $profanity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profanityRepository->create($profanity);

            return $this->redirectToRoute('admin_profanities');
        }

        return $this->render('@AdminBundle/profanity/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/scheldwoord/verwijder/{id}', name: 'admin_profanity_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $profanity = $this->profanityRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->profanityRepository->delete($profanity);
        }

        return $this->redirectToRoute('admin_profanities');
    }
}
