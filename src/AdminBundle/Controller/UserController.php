<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\CreateUserType;
use App\AdminBundle\Form\DeleteUserType;
use App\AdminBundle\Form\UpdateUserType;
use App\Controller\AuthController;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    #[
        Route('/gebruiker', name: 'adminUser', defaults: ['page' => '1']),
        Route('/gebruiker/pagina/{page<[1-9]\d*>}', name: 'adminUserIndexPaginated'),
    ]
    public function view(int $page): Response
    {
        $users = $this->userRepository->findAllPaginated($page);

        return $this->render('@AdminBundle/user/view.html.twig', [
            'paginator' => $users,
        ]);
    }

    #[Route('/gebruiker/wijzig/{id}', name: 'adminUserEdit')]
    public function edit(Request $request, User $user): Response
    {
        $formUpdate = $this->createForm(UpdateUserType::class, $user, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteUserType::class, $user, [
            'action' => $this->generateUrl('adminUserDelete', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            if (is_null($formUpdate->get('plainPassword')->getData())) {
                $this->userRepository->update();
            } else {
                $this->userRepository->upgradePassword($user, $formUpdate->get('plainPassword')->getData());
            }

            return $this->redirectToRoute('adminUser');
        }

        return $this->render('@AdminBundle/user/edit/view.html.twig', [
            'formUpdate' => $formUpdate->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/gebruiker/toevoegen', name: 'adminUserCreate')]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->create($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('adminUser');
        }

        return $this->render('@AdminBundle/user/new/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gebruiker/verwijder/{id}', name: 'adminUserDelete')]
    public function delete(Request $request, User $user): RedirectResponse
    {
        $form = $this->createForm(DeleteUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->unlinkImage($this->getParameter('kernel.environment'),
                $this->getParameter('kernel.project_dir'));
            $this->userRepository->delete($user);
        }

        return $this->redirectToRoute('adminUser');
    }
}
