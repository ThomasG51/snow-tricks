<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickType;
use App\Repository\TrickRepository;
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
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('tricks/index.html.twig', [
            'tricks' => $trickRepository->findAllTenByTen(0, 5)
        ]);
    }


    /**
     * Show specific tricks
     *
     * @Route("/show/{id}", name="show_tricks")
     * @param $id
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function show($id, TrickRepository $trickRepository): Response
    {
        return $this->render('tricks/show.html.twig', [
            'trick' => $trickRepository->find($id)
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
        $trick = new Trick();
        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if ($formTrick->isSubmitted() && $formTrick->isValid())
        {
            $trick->setCreatedAt(new \DateTime());

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('show_tricks', [
                'id' => $trick->getId()
            ]);
        }

        return $this->render('tricks/create.html.twig', [
            'formTricks' => $formTrick->createView()
        ]);
    }


    /**
     * Load more tricks ten by ten
     *
     * @Route("/load/{firstItem}/{nbItems}", name="load")
     * @param int $firstItem
     * @param int $nbItems
     * @param TrickRepository $trickRepository
     * @return Response
     */
    public function loadMore(int $firstItem, int $nbItems, TrickRepository $trickRepository) : Response
    {
        return $this->json($trickRepository->findAllTenByTen($firstItem, $nbItems));
    }
}
