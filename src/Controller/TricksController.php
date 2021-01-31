<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Type;
use App\Entity\User;
use App\Form\TricksType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TricksRepository $tricksRepository
     * @return Response
     */
    public function index(TricksRepository $tricksRepository): Response
    {
        return $this->render('tricks/index.html.twig', [
            'tricks' => $tricksRepository->findAll()
        ]);
    }

    /**
     * @Route("/show", name="show_tricks")
     * @return Response
     */
    public function show(): Response
    {
        return $this->render('tricks/show.html.twig', []);
    }

    /**
     * @Route("/create/tricks", name="create_tricks")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $tricks = new Tricks();

        $form = $this->createForm(TricksType::class, $tricks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $tricks->setDifficulty(2.0);
            $tricks->setCreatedAt(new \DateTime());

            $manager->persist($tricks);
        }

        $manager->flush();

        return $this->render('tricks/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
