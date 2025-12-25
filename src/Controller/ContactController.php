<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\PageRepositoryInterface;
use App\Service\ProfanityCheckService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Address;
use Exception;

class ContactController extends Controller
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly ProfanityCheckService $profanityCheckService,
    ) {
    }

    #[Route('/contact', name: 'contact')]
    public function view(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

        /**
         * Mail is only send in the prod environment with a reCAPTCHA check.
         */
        $error = null;
        $success = null;
        if ($form->isSubmitted()
            && $form->isValid()
            && $this->validateReCaptcha($form->get('re_captcha_token')->getData())
            && $this->getParameter('kernel.environment') === 'prod') {
            try {
                $this->profanityCheckService->check($form->get('name')->getData());
                $this->profanityCheckService->check($form->get('subject')->getData());
                $this->profanityCheckService->check($form->get('message')->getData());

                $email = new Email()
                    ->from(new Address('noreply@mailing.smuldieet.nl', strip_tags($form->get('name')->getData())))
                    ->replyTo($form->get('email')->getData())
                    ->to('info@smuldieet.nl')
                    ->subject(strip_tags($form->get('subject')->getData()))
                    ->html($form->get('message')->getData());

                try {
                    $this->mailer->send($email);
                    $success = 'Bericht verzonden.';
                } catch (TransportExceptionInterface) {
                    $error = 'Kon e-mail niet verzenden.';
                }
                $form = $this->createForm(ContactType::class);
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        } elseif ($form->isSubmitted()
            && $form->isValid()
            && !$this->validateReCaptcha($form->get('re_captcha_token')->getData())
            && $this->getParameter('kernel.environment') === 'prod') {
            $error = 'No bots allowed.';
        } elseif ($form->isSubmitted()) {
            $error = 'Could not send mail, because this is not the prod environment.';
        }

        return $this->render('contact/view.html.twig', [
            'form' => $form->createView(),
            'reCaptchaKey' => $this->getParameter('re_captcha_key'),
            'success' => $success,
            'error' => $error,
            'page' => $this->pageRepository->findOneBy(['slug' => 'contact']),
        ]);
    }

    private function validateReCaptcha(string $token): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=" . $this->getParameter('re_captcha_secret') .
            '&response=' . $token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result = json_decode($response);

        if ($httpCode !== 200 || $result->success === false) {
            return false;
        }

        return true;
    }
}
