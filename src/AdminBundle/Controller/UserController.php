<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\CreateUserType;
use App\Form\DeleteType;
use App\AdminBundle\Form\UpdateUserType;
use App\Controller\AuthController;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

class UserController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    #[
        Route('/gebruikers', name: 'admin_users', defaults: ['page' => '1']),
        Route('/gebruikers/pagina/{page<[1-9]\d*>}', name: 'admin_users_index_paginated'),
    ]
    public function view(int $page): Response
    {
        $users = $this->userRepository->findAllPaginated($page);

        return $this->render('@AdminBundle/user/view.html.twig', [
            'paginator' => $users,
        ]);
    }

    #[Route('/gebruiker/wijzig/{id}', name: 'admin_user_edit')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->userRepository->get($id);

        $oldExtension = $user->getImageExtension();

        $form = $this->createForm(UpdateUserType::class, $user, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $user, [
            'action' => $this->generateUrl('admin_user_delete', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (is_null($form->get('plain_password')->getData())) {
                    $user->setUpdatedAt(time());
                    $this->userRepository->update($user, $oldExtension);
                } else {
                    $this->userRepository->upgradePassword(
                        $user,
                        $form->get('plain_password')->getData(),
                        $oldExtension
                    );
                }

                return $this->redirectToRoute('admin_users');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/user/edit.html.twig', [
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/gebruiker/toevoegen', name: 'admin_user_create')]
    public function new(Request $request): Response
    {
        $user = new User();
        $user->setCreatedAt(time());
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userRepository->create($user, $form->get('plain_password')->getData());

                return $this->redirectToRoute('admin_users');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gebruiker/verwijder/{id}', name: 'admin_user_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $user = $this->userRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->delete($user);
        }

        return $this->redirectToRoute('admin_users');
    }
}
