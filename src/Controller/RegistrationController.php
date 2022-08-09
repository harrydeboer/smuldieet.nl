<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Route('/registreren', name: 'appRegister')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $this->checkImage($form)) {
            $this->userRepository->create($user, $form->get('plainPassword')->getData());

            $token = new UsernamePasswordToken($user, $user->getPassword(), $user->getRoles());

            $this->tokenStorage->setToken($token);
            $this->moveImage($user, $form->get('image')->getData());

            return $this->redirectToRoute('homepage');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return parent::getUser();
    }

    private function moveImage(User $user, ?UploadedFile $image)
    {
        $user->moveImage($image, $this->getParameter('kernel.environment'),
            $this->getParameter('kernel.project_dir'));
    }
}
