<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AuthController
{
    #[Route('/', name: 'user_homepage')]
    public function view(): Response
    {
        return $this->render('@UserBundle/homepage/view.html.twig');
    }
}
