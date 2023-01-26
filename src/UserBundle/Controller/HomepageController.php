<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use App\Repository\UserRepositoryInterface;
use App\Service\UploadedImageService;
use App\UserBundle\Form\UserType;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AuthController
{
    public function __construct(
        private readonly UserRepositoryInterface   $userRepository,
        private readonly UploadedImageService      $uploadedImageService,
    ) {
    }

    #[Route('/', name: 'user_homepage')]
    public function view(): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser(), [
            'action' => $this->generateUrl('user_edit', ['id' => $this->getUser()->getId()]),
            'method' => 'POST',
        ]);

        return $this->render('@UserBundle/homepage/view.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/wijzig/{id}', name: 'user_edit')]
    public function edit(Request $request, int $id): Response
    {
        $user = $this->userRepository->get($id);

        $oldExtension = $this->getUser()->getImageExtension();

        $formUpdate = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
        ]);

        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {
            try {
                $user->setUpdatedAt(time());
                $this->userRepository->update();

                $this->uploadedImageService->moveImage(
                    $this->getUser(),
                    $oldExtension,
                );

            } catch (Exception $exception) {
                $this->addFlash('user_form_exception', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('user_homepage');
    }
}
