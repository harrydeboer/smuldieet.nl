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
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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
        Route('/gebruikers', name: 'adminUser', defaults: ['page' => '1']),
        Route('/gebruikers/pagina/{page<[1-9]\d*>}', name: 'adminUserIndexPaginated'),
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
        $oldExtension = $user->getImageExtension();

        $formUpdate = $this->createForm(UpdateUserType::class, $user, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, $user, [
            'action' => $this->generateUrl('adminUserDelete', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                if (is_null($formUpdate->get('plainPassword')->getData())) {
                    $this->userRepository->update();
                } else {
                    $this->userRepository->upgradePassword($user, $formUpdate->get('plainPassword')->getData());
                }

                $user->moveImage($this->getParameter('kernel.environment'),
                    $this->getParameter('kernel.project_dir'),
                    $formUpdate->get('image')->getData(), $oldExtension);

                return $this->redirectToRoute('adminUser');
            } catch (BadRequestException $exception) {
                $formUpdate->addError(new FormError($exception->getMessage()));
            }
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
            try {
                $this->userRepository->create($user, $form->get('plainPassword')->getData());
                $user->moveImage($this->getParameter('kernel.environment'),
                    $this->getParameter('kernel.project_dir'), $form->get('image')->getData());

                return $this->redirectToRoute('adminUser');
            } catch (BadRequestException $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/user/new/view.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/gebruiker/verwijder/{id}', name: 'adminUserDelete')]
    public function delete(Request $request, User $user): RedirectResponse
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->unlinkImage($this->getParameter('kernel.environment'),
                $this->getParameter('kernel.project_dir'));
            $this->userRepository->delete($user);
        }

        return $this->redirectToRoute('adminUser');
    }
}
