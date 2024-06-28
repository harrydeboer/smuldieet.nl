<?php

declare(strict_types=1);

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ErrorController extends Controller
{
    public function __construct(
        private readonly Environment $environment,
    ) {
    }

    public function show(Throwable $exception): ?Response
    {
        /**
         * When the exception has a status code the matching status code page is rendered.
         */
        if (method_exists($exception, 'getStatusCode')) {
            $statusCodeString = (string) $exception->getStatusCode();

            /**
             * When the exception is because of no verification then send
             * the user to the send_verification_email_again route.
             */
            if ($statusCodeString === '403' && !$this->getUser()->isVerified()) {
                return $this->redirectToRoute('send_verification_email_again');
            }

            $templatePath = 'error/' . $statusCodeString . '.html.twig';
            if ($this->environment->getLoader()->exists($templatePath)) {
                return $this->render(
                    $templatePath,
                    ['message' => $exception->getMessage()],
                    new Response('', (int) $statusCodeString),
                );
            }
        }

        return $this->render('error/500.html.twig', ['message' => 'Er ging iets fout.'], new Response('', 500));
    }
}
