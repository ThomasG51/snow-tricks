<?php

namespace App\Validator;

use App\Repository\TrickRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueTrickValidator extends ConstraintValidator
{
    /**
     * @var TrickRepository
     */
    private TrickRepository $trickRepository;
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    public function __construct(TrickRepository $trickRepository, SluggerInterface $slugger)
    {
        $this->trickRepository = $trickRepository;
        $this->slugger = $slugger;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UniqueTrick */

        $value->setSlug(strtolower($this->slugger->slug($value->getName() . ' ' . $value->getCategory()->getName(), '-')));
        $trick = $this->trickRepository->findOneBySlug($value->getSlug());

        if ($trick === null || $trick->getId() === $value->getId())
        {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value->getName())
            ->atPath("name")
            ->addViolation();
    }
}
