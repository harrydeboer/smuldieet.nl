<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\Controller\AuthController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AuthController
{
    #[Route('/', name: 'adminHomepage')]
    public function view(): Response
    {
        return $this->render('@AdminBundle/homepage/view.html.twig');
    }
}
