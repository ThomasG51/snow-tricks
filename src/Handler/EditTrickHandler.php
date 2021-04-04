<?php


namespace App\Handler;


use App\Entity\Trick;

class EditTrickHandler extends AbstractHandler
{
    /**
     * Form data processing
     *
     * @param Trick $trick
     * @param $token
     */
    public function process($trick, $token)
    {
        $trick->setModifiedAt(new \DateTime());
    }
}