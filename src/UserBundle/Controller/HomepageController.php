<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use App\Repository\UserRepositoryInterface;
use App\UserBundle\Form\UserType;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface   $userRepository,
    ) {
    }

    #[Route('/', name: 'user_homepage')]
    public function view(): Response
    {
        return $this->render('@UserBundle/homepage/view.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/wijzig/{id}', name: 'user_edit')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->userRepository->get($id);

        $oldExtension = $this->getUser()->getImageExtension();

        $form = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setUpdatedAt(time());
                $this->userRepository->update($user, $oldExtension);

                return $this->redirectToRoute('user_homepage');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }
        $user->setImage(null);

        return $this->render('@UserBundle/homepage/edit.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
