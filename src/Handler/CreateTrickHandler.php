<?php


namespace App\Handler;


use App\Entity\Trick;

class CreateTrickHandler extends AbstractHandler
{
    /**
     * Form data processing
     *
     * @param Trick $trick
     * @param $token
     */
    public function process($trick, $token)
    {
            $trick->setCreatedAt(new \DateTime());
            $trick->setUser($token->getToken()->getUser());

            foreach($trick->getVideos() as $video)
            {
                // Pattern for youtube url
                preg_match('/^(\w+:\/\/\w+\.\w+\.*\w+\/(\w+\?v=)*)(\w+)((\&\S+)*)$/',$video->getUrl(), $videoId);
                $video->setUrl($videoId[3]);
            }
    }
}