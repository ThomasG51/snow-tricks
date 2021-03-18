<?php

namespace App\Controller;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /**
     * @Route("/reset-password-request", name="reset-password-request")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param ResetPasswordRequestRepository $resetPasswordRepository
     * @return Response
     */
    public function resetPasswordRequest(Request $request, UserRepository $userRepository, ResetPasswordRequestRepository $resetPasswordRepository, MailerInterface $mailer): Response
    {
        if($request->request->get('email'))
        {
            $user = $userRepository->findOneByEmail($request->request->get('email'));
            $oldRequest = $resetPasswordRepository->findOneByUser($user);

            if(!is_null($oldRequest))
            {
                $oldRequest->setCreatedAt(new \DateTime());
                $oldRequest->setExpiredAt(new \DateTime('+1 day'));
                $oldRequest->setToken($request->request->get('token'));

                $this->getDoctrine()->getManager()->persist($oldRequest);
                $this->getDoctrine()->getManager()->flush();
            }
            else
            {
                $resetPasswordRequest = new ResetPasswordRequest();
                $resetPasswordRequest->setCreatedAt(new \DateTime());
                $resetPasswordRequest->setExpiredAt(new \DateTime('+1 day'));
                $resetPasswordRequest->setToken($request->request->get('token'));
                $resetPasswordRequest->setUser($user);

                $this->getDoctrine()->getManager()->persist($resetPasswordRequest);
                $this->getDoctrine()->getManager()->flush();
            }

            $email = new Email();
            $email->from('hello@snowtricks.fr');
            $email->to($user->getEmail());
            $email->subject('Réinitialiser mon mot de passe!');
            if(!is_null($oldRequest))
            {
                $email->html('<p>Nouvelle demande de reinitialisation du mot de passe:</p> <a href="http://localhost:8000/reset-password?token=' . $oldRequest->getToken() . '">Cliquez ici.</a>');
            }
            else
            {
                $email->html('<p>Pour réinitialiser votre mot de passe:</p> <a href="http://localhost:8000/reset-password?token=' . $resetPasswordRequest->getToken() . '">Cliquez ici.</a>');
            }

            $mailer->send($email);

            return $this->redirectToRoute('home');
        }

        return $this->render('security/reset_password_request.html.twig',  []);
    }


    /**
     * @Route("/reset-password", name="reset-password")
     * @param Request $request
     * @param ResetPasswordRequestRepository $resetPasswordRequestRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function resetPassword(Request $request, ResetPasswordRequestRepository $resetPasswordRequestRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $resetPasswordRequest = $resetPasswordRequestRepository->findOneByToken($request->query->get('token'));

        if($resetPasswordRequest == null)
        {
            throw new \Exception('Token invalid');
        }

        if($resetPasswordRequest->getExpiredAt() < new \DateTime())
        {
            throw new \Exception('Token expired');
        }

        $form = $this->createForm(ResetPasswordType::class, null);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $resetPasswordRequest->getUser();
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->remove($resetPasswordRequest);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
