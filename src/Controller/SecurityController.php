<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PageRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use LogicException;

class SecurityController extends Controller
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/inloggen', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'isLoggedIn' => !is_null($this->getUser()),
            'last_username' => $lastUsername,
            'username' => $this->getUser()?->getUsername(),
            'roles' => $this->getUser()?->getRoles(),
            'error' => $error,
            'page' => $this->pageRepository->findOneBy(['slug' => 'inloggen']),
            ]);
    }

    #[Route('/uitloggen', name: 'app_logout')]
    public function logout()
    {
        throw new LogicException('This method can be blank - ' .
            'it will be intercepted by the logout key on your firewall.');
    }
}
