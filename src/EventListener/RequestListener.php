<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestListener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        // Set multiple headers simultaneously
        $response->headers->set(
            'Content-Security-Policy', "default-src 'self'; style-src 'self' 'unsafe-inline' " .
            "https://fonts.googleapis.com/css2 " .
            "https://www.gstatic.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data:; " .
            "script-src 'self' https://www.googletagmanager.com/gtag/js " .
            "https://www.google.com/recaptcha/api.js https://www.gstatic.com; " .
            "frame-src 'self' https://www.google.com/",
        );
    }
}
