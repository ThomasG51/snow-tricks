<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * Delete comment
     *
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Comment $comment, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CAN_DELETE', $comment, 'Vous ne pouvez pas supprimer le commentaire d\'un autre utilisateur.');

        if($this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $request->request->get('token')))
        {
            $this->getDoctrine()->getManager()->remove($comment);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        throw new \Exception('Delete comment failed', 400);
    }
}
