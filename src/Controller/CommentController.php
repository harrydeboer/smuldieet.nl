<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepositoryInterface;
use App\Repository\PageRepositoryInterface;
use App\Repository\RecipeRepositoryInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
        private readonly RecipeRepositoryInterface $recipeRepository,
        private readonly PageRepositoryInterface $pageRepository,
    ) {
    }

    #[Route('/commentaar/recipe/{recipeId}', name: 'recipe_comment_create')]
    public function newRecipeComment(Request $request, int $recipeId): RedirectResponse
    {
        $comment = new Comment();
        $recipe = $this->recipeRepository->get($recipeId);
        $comment->setRecipe($recipe);
        $form = $this->createForm(CommentType::class, $comment);

        /**
         * When creating a comment it is checked that the recipe is not pending except when the current user owns it.
         */
        if ($recipe->getIsPending() && $recipe->getUser()->getId() !== $this->getUser()->getId()) {
            throw new NotFoundHttpException('Dit recept can niet worden getoond.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setTimestamp(time());
            $comment->setIsPending(true);

            try {
                $this->commentRepository->create($comment);
            } catch (Exception $exception) {
                $this->addFlash('comment_exception', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('recipe_single', ['id' => $recipe->getId()]);
    }

    #[Route('/commentaar/page/{pageId}', name: 'page_comment_create')]
    public function newPageComment(Request $request, int $pageId): RedirectResponse
    {
        $comment = new Comment();
        $page = $this->pageRepository->get($pageId);
        $comment->setPage($page);
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setTimestamp(time());

            try {
                $this->commentRepository->create($comment);
            } catch (Exception $exception) {
                $this->addFlash('comment_exception', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('page_cms', ['slug' => $page->getSlug()]);
    }
}
