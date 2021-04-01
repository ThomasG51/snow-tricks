<?php


namespace App\Handler;


use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditTrickHandler
{
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $manager;
    private SluggerInterface $slugger;
    private $formTrick;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->slugger = $slugger;
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
            $trick->setSlug(strtolower($this->slugger->slug($trick->getName() . ' ' . $trick->getCategory()->getName(), '-')));
            $trick->setModifiedAt(new \DateTime());

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