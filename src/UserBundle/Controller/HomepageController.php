<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    #[Route('/', name: 'user_homepage')]
    public function view(Request $request): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser(), [
            'action' => $this->generateUrl('user_edit', ['id' => $this->getUser()->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->update();
        }

        return $this->render('@UserBundle/homepage/view.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            ]);
    }

    #[Route('/user/wijzig/{id}', name: 'user_edit')]
    public function edit(Request $request, User $user): Response
    {
        $formUpdate = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            $this->userRepository->update();
        }

        return $this->redirectToRoute('user_homepage');
    }
}
