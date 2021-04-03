<?php


namespace App\Handler;


use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CreateTrickHandler extends AbstractHandler
{
    private TokenStorageInterface $token;

    /**
     * Form data processing
     *
     * @param Trick $trick
     * @return bool
     */
    public function process($trick)
    {
            $trick->setCreatedAt(new \DateTime());
            $trick->setUser($this->token->getToken()->getUser());

            foreach($trick->getVideos() as $video)
            {
                // Pattern for youtube url
                preg_match('/^(\w+:\/\/\w+\.\w+\.*\w+\/(\w+\?v=)*)(\w+)((\&\S+)*)$/',$video->getUrl(), $videoId);
                $video->setUrl($videoId[3]);
            }
    }
}