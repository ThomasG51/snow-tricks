<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * Delete user
     *
     * @Route("/user/delete/{id}", name="delete_user")
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, User $user, EntityManagerInterface $manager): Response
    {
        if($this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->get('token')))
        {
            $manager->remove($user);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        throw new \Exception('Impossible de supprimer l\'utilisateur');
    }
}
