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
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function delete(Comment $comment, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $request->request->get('token')))
        {
            $manager->remove($comment);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        throw new \Exception('Delete comment failed', 400);
    }
}
