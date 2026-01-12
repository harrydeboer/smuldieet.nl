<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class Listener
{
    public function onKernelResponse(ResponseEvent $event): void
    {
//        $response = $event->getResponse();
//
//        /**
//         * Set the Content Security Policy header.
//         */
//        $response->headers->set(
//            'Content-Security-Policy', "default-src 'self'; style-src 'self' " .
//            "https://fonts.googleapis.com/css2 " .
//            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css " .
//            "https://www.gstatic.com; " .
//            "connect-src 'self' https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css.map " .
//            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js.map;" .
//            "font-src 'self' https://fonts.gstatic.com; " .
//            "img-src 'self' data:; " .
//            "script-src 'self' https://www.googletagmanager.com/gtag/js " .
//            "https://code.jquery.com/jquery-3.7.1.min.js " .
//            "https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js " .
//            "https://www.google.com/recaptcha/api.js https://www.gstatic.com; " .
//            "frame-src 'self' https://www.google.com/",
//        );
    }
}
