<?php

declare(strict_types=1);

namespace App\UserBundle\Controller;

use App\Controller\AuthController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SavedRecipesController extends AuthController
{
    #[
        Route('/bewaarde-recepten', name: 'user_saved_recipes'),
    ]
    public function view(): Response
    {
        return $this->render('@UserBundle/savedRecipes/view.html.twig', [
            'savedRecipes' => $this->getUser()->getSavedRecipes(),
        ]);
    }
}
