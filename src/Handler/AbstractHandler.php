<?php


namespace App\Handler;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

abstract class AbstractHandler
{
    private TokenStorageInterface $token;
    private FormFactoryInterface $formFactory;
    private EntityManagerInterface $manager;
    private FormInterface $form;

    /**
     * @Required
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface $token
     */
    public function load(FormFactoryInterface $formFactory, EntityManagerInterface $manager, TokenStorageInterface $token)
    {
        $this->token = $token;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
    }

    abstract public function process($entity, $token);

    /**
     * Handle form
     * @param Request $request
     * @param $formType
     * @param $entity
     * @return bool
     */
    public function handle(Request $request, $formType, $entity): bool
    {
        $this->form = $this->formFactory->create($formType, $entity);
        $this->form->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid())
        {
            $this->process($entity, $this->token);

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