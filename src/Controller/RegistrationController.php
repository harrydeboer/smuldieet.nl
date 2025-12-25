<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\VerifyType;
use App\Repository\PageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface    $userRepository,
        private readonly PageRepositoryInterface    $pageRepository,
        private readonly TokenStorageInterface      $tokenStorage,
        private readonly KernelInterface            $kernel,
        private readonly MailerInterface            $mailer,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/registreren', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $user->setCreatedAt(time());
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->userRepository->create($user, $form->get('plain_password')->getData());

                $this->sendVerificationMail($user);

                $this->addFlash('mustVerify', 'Je e-mailadres moet geverifieerd worden. Check je inbox.');

                $token = new UsernamePasswordToken($user, $user->getPassword(), $user->getRoles());

                $this->tokenStorage->setToken($token);

                return $this->redirectToRoute('homepage');
            } catch (Exception $exception) {

                $form->addError(new FormError($exception->getMessage()));
            }
        }
        $user->setImage(null);

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'page' => $this->pageRepository->findOneBy(['slug' => 'registreren']),
        ]);
    }

    #[Route('/verifieer', name: 'registration_confirmation_route')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $oldExtension = $user->getImageExtension();

        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string) $user->getId(), $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Je e-mail adres is geverifieerd.');

        $this->getUser()->setVerified(true);
        $this->userRepository->update($user, $oldExtension);

        $token = new UsernamePasswordToken($user, $user->getPassword(), $user->getRoles());

        $this->tokenStorage->setToken($token);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/verstuur-verificatie-e-mail-opnieuw', name: 'send_verification_email_again')]
    public function sendVerificationEmailAgain(Request $request): Response
    {
        $success = null;
        $error = null;

        $form = $this->createForm(VerifyType::class);
        $form->handleRequest($request);

        if ($this->getUser()->isVerified()) {
            $success = 'Je e-mail is al geverifieerd.';
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->sendVerificationMail($this->getUser());
            if ($result) {
                $success = 'Er is een verificatie mail verstuurd.';
            } else {
                $error = 'Kon geen mail versturen.';
            }
        }

        return $this->render('registration/send_verification_email_again.html.twig', [
            'form' => $form->createView(),
            'success' => $success,
            'error' => $error,
        ]);
    }

    /**
     * Verification of email only in production.
     * @throws TransportExceptionInterface
     */
    private function sendVerificationMail(User $user): bool
    {
        if ($this->kernel->getEnvironment() === 'prod') {

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation_route',
                (string) $user->getId(),
                $user->getEmail()
            );

            $email = new TemplatedEmail();
            $email->from(new Address('noreply@mailing.smuldieet.nl', 'Smuldieet'));
            $email->to($user->getEmail());
            $email->subject('Verifieer je e-mail op smuldieet.nl');
            $email->htmlTemplate('registration/confirmation_email.html.twig');
            $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);

            $this->mailer->send($email);

            return true;
        }

        return false;
    }
}
