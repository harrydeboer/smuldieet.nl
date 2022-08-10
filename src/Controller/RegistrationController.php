<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\VerifyType;
use App\Repository\UserRepositoryInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly KernelInterface $kernel,
        private readonly MailerInterface $mailer,
        private readonly VerifyEmailHelperInterface $verifyEmailHelper,
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

            $this->sendVerificationMail($user);

            $this->addFlash('mustVerify', 'Je e-mailadres moet geverifieerd worden. Check je inbox.');

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

    #[Route('/verify', name: 'registration_confirmation_route')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        // Do not get the User's id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                (string) $user->getId(), $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('appRegister');
        }

        // Mark your user as verified. e.g. switch a User::verified property to true

        $this->addFlash('success', 'Your email address has been verified.');

        $this->getUser()->setIsVerified(true);
        $this->userRepository->update();

        $token = new UsernamePasswordToken($user, $user->getPassword(), $user->getRoles());

        $this->tokenStorage->setToken($token);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/send-verification-email-again', name: 'sendVerificationEmailAgain')]
    public function sendVerificationEmailAgain(Request $request): Response
    {
        $success = null;
        $error = null;

        $form = $this->createForm(VerifyType::class);
        $form->handleRequest($request);

        if ($this->getUser()->isVerified()) {
            $success = 'Your email is already verified.';
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->sendVerificationMail($this->getUser());
            if ($result) {
                $success = 'Successfully send a verification mail.';
            } else {
                $error = 'Could not send mail.';
            }
        }

        return $this->render('registration/sendVerificationEmailAgain.html.twig', [
            'form' => $form->createView(),
            'success' => $success,
            'error' => $error,
        ]);
    }

    /**
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
            $email->from(new Address('noreply@smuldieet.nl', 'Smuldieet'));
            $email->to($user->getEmail());
            $email->subject('Verify your email on smuldieet.nl');
            $email->htmlTemplate('registration/confirmation_email.html.twig');
            $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);

            $this->mailer->send($email);

            return true;
        }

        return false;
    }

    private function moveImage(User $user, ?UploadedFile $image)
    {
        $user->moveImage($image, $this->getParameter('kernel.environment'),
            $this->getParameter('kernel.project_dir'));
    }
}
