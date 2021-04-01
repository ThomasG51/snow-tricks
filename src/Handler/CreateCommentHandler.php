<?php


namespace App\Handler;


use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateCommentHandler
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $manager;
    private  $formComment;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $manager)
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    /**
     * Form data processing
     *
     * @param Request $request
     * @param Comment $comment
     * @param Trick $trick
     * @return bool
     */
    public function processing(Request $request, Comment $comment, Trick $trick): bool
    {
        $this->formComment = $this->formFactory->create(CommentType::class, $comment);
        $this->formComment->handleRequest($request);

        if($this->formComment->isSubmitted() && $this->formComment->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $this->manager->persist($comment);
            $this->manager->flush();

            return true;
        }

        return false;
    }

    /**
     * Return form to be rendered
     *
     * @return mixed
     */
    public function form()
    {
        return $this->formComment;
    }
}