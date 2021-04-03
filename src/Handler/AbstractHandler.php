<?php


namespace App\Handler;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler
{
    private Request $request;
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $manager;
    private FormInterface $form;

    /**
     * @Required
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $manager
     * @param FormInterface $form
     */
    public function load(Request $request, FormFactoryInterface $formFactory, EntityManagerInterface $manager, FormInterface $form)
    {
        $this->request = $request;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->form = $form;
    }

    abstract public function process($entity);

    /**
     * Handle form
     * @param $formType
     * @param $entity
     * @return bool
     */
    public function handle($formType, $entity): bool
    {
        $this->form = $this->formFactory->create($formType, $entity);
        $this->form->handleRequest($this->request);

        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->process($entity);

            $this->manager->persist($entity);
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
        return $this->form;
    }
}