<?php


namespace App\Handler;


use App\Entity\Comment;

class CreateCommentHandler extends AbstractHandler
{
    /**
     * Form data processing
     *
     * @param Comment $comment
     * @param $token
     */
    public function process($comment, $token)
    {
        $comment->setCreatedAt(new \DateTime());
        $comment->setUser($token->getToken()->getUser());
    }
}