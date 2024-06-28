<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Repository\PageRepositoryInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ChangePasswordController extends AuthController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/verander-wachtwoord', name: 'change_password')]
    public function changePassword(Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->userRepository->upgradePassword($this->getUser(), $form->get('plain_password')->getData());

            return $this->redirectToRoute('homepage');
        }

        return $this->render('/security/change_password.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['slug' => 'verander-wachtwoord']),
        ]);
    }
}
