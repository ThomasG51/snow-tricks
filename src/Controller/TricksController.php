<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Tricks;
use App\Form\MediaType;
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
     * Show all tricks on home page
     *
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
     * Show specific tricks
     *
     * @Route("/show/{id}", name="show_tricks")
     * @param $id
     * @param TricksRepository $tricksRepository
     * @return Response
     */
    public function show($id, TricksRepository $tricksRepository): Response
    {
        return $this->render('tricks/show.html.twig', [
            'trick' => $tricksRepository->find($id)
        ]);
    }


    /**
     * Create new tricks
     *
     * @Route("/create/tricks", name="create_tricks")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $media = new Media();
        $formMedia = $this->createForm(MediaType::class, $media);

        $tricks = new Tricks();
        $formTricks = $this->createForm(TricksType::class, $tricks);
        $formTricks->handleRequest($request);

        if ($formTricks->isSubmitted() && $formTricks->isValid())
        {
            $tricks->setCreatedAt(new \DateTime());

            $manager->persist($tricks);
            $manager->flush();

            return $this->redirectToRoute('show_tricks', [
                'id' => $tricks->getId()
            ]);
        }

        return $this->render('tricks/create.html.twig', [
            'formTricks' => $formTricks->createView(),
            'formMedia' => $formMedia->createView()
        ]);
    }
}
