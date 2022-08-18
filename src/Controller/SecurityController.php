<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use LogicException;

class SecurityController extends Controller
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/inloggen', name: 'appLogin')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class);

        return $this->render('security/login.html.twig', [
            'isLoggedIn' => !is_null($this->getUser()),
            'last_username' => $lastUsername,
            'username' => $this->getUser()?->getUsername(),
            'roles' => $this->getUser()?->getRoles(),
            'error' => $error,
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['title' => 'Inloggen']),
            ]);
    }

    #[Route('/uitloggen', name: 'appLogout')]
    public function logout()
    {
        throw new LogicException('This method can be blank - ' .
            'it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @return ?User
     */
    protected function getUser(): ?UserInterface
    {
        return parent::getUser();
    }
}
