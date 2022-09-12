<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\CreateUserType;
use App\Form\DeleteType;
use App\AdminBundle\Form\UpdateUserType;
use App\Controller\AuthController;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Service\UploadedImageService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class UserController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UploadedImageService    $uploadedImageService,
    ) {
    }

    #[
        Route('/gebruikers', name: 'admin_user', defaults: ['page' => '1']),
        Route('/gebruikers/pagina/{page<[1-9]\d*>}', name: 'admin_user_index_paginated'),
    ]
    public function view(int $page): Response
    {
        $users = $this->userRepository->findAllPaginated($page);

        return $this->render('@AdminBundle/user/view.html.twig', [
            'paginator' => $users,
        ]);
    }

    #[Route('/gebruiker/wijzig/{id}', name: 'admin_user_edit')]
    public function edit(Request $request, User $user): Response
    {
        $oldExtension = $user->getImageExtension();

        $formUpdate = $this->createForm(UpdateUserType::class, $user, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $user, [
            'action' => $this->generateUrl('admin_user_delete', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                if (is_null($formUpdate->get('plain_password')->getData())) {
                    $this->userRepository->update();
                } else {
                    $this->userRepository->upgradePassword($user, $formUpdate->get('plain_password')->getData());
                }

                $this->uploadedImageService->moveImage(
                    $user,
                    $oldExtension,
                );

                return $this->redirectToRoute('admin_user');
            } catch (Exception $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/user/edit.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/gebruiker/toevoegen', name: 'admin_user_create')]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userRepository->create($user, $form->get('plain_password')->getData());
                $this->uploadedImageService->moveImage($user);

                return $this->redirectToRoute('admin_user');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gebruiker/verwijder/{id}', name: 'admin_user_delete')]
    public function delete(Request $request, User $user): RedirectResponse
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadedImageService->unlinkImage($user);
            $this->userRepository->delete($user);
        }

        return $this->redirectToRoute('admin_user');
    }
}
