<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function catchAll(): Response
    {
        throw new NotFoundHttpException('De pagina is bestaat niet.');
    }
}
