<?php


namespace App\Handler;


use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateTrickHandler
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $manager;
    private $formTrick;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $manager)
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    /**
     * Form data processing
     *
     * @param Request $request
     * @param Trick $trick
     * @return bool
     */
    public function processing(Request $request, Trick $trick): bool
    {
        $this->formTrick = $this->formFactory->create(TrickType::class, $trick);
        $this->formTrick->handleRequest($request);

        if ($this->formTrick->isSubmitted() && $this->formTrick->isValid())
        {
            $trick->setCreatedAt(new \DateTime());

            // TODO set user
            //$trick->setUser();

            foreach($trick->getVideos() as $video)
            {
                // Pattern for youtube url
                preg_match('/^(\w+:\/\/\w+\.\w+\.*\w+\/(\w+\?v=)*)(\w+)((\&\S+)*)$/',$video->getUrl(), $videoId);
                $video->setUrl($videoId[3]);
            }

            $this->manager->persist($trick);
            $this->manager->flush();

            return true;
        }

        return false;
    }

    /**
     * Return form to be rendered
     * @return mixed
     */
    public function form()
    {
        return $this->formTrick;
    }
}