<?php

declare(strict_types=1);

namespace App\AdminBundle\Controller;

use App\AdminBundle\Form\ApproveType;
use App\Form\DeleteType;
use App\Controller\AuthController;
use App\Repository\CommentRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;

/**
 * The pending status of comments is removed or comments are deleted.
 */
class CommentController extends AuthController
{
    public function __construct(
        private readonly CommentRepositoryInterface $commentRepository,
    ) {
    }

    #[Route('/commentaar', name: 'admin_comments')]
    public function view(): Response
    {
        $comments = $this->commentRepository->findAllPendingComments();

        return $this->render('@AdminBundle/comment/view.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/commentaar/wijzig/{id}', name: 'admin_comment_edit')]
    public function edit(Request $request, int $id): Response
    {
        $comment = $this->commentRepository->get($id);

        $form = $this->createForm(ApproveType::class, null, [
            'method' => 'POST',
        ]);

        $formDelete = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('admin_comment_delete', ['id' => $comment->getId()]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $comment->setUpdatedAt(time());
                $comment->setPending(false);
                $this->commentRepository->update($comment);

                return $this->redirectToRoute('admin_comments');
            } catch (Exception $exception) {
                $form->addError(new FormError($exception->getMessage()));
            }
        }

        return $this->render('@AdminBundle/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'formDelete' => $formDelete->createView(),
        ]);
    }

    #[Route('/commentaar/verwijder/{id}', name: 'admin_comment_delete')]
    public function delete(Request $request, int $id): RedirectResponse
    {
        $comment = $this->commentRepository->get($id);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->delete($comment);
        }

        return $this->redirectToRoute('admin_comments');
    }
}
