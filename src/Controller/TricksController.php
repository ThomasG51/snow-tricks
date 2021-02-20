<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            'tricks' => $trickRepository->find5By5(0, 5)
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

            foreach($trick->getVideos() as $video)
            {
                // Reformatting youtube url to be used in iframe
                $url = explode('/', $video->getUrl());
                $url = 'https://youtube.com/embed/' . array_pop($url);

                $video->setUrl($url);
            }

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
     * Load more tricks five by five
     *
     * @Route("/load/{firstItem}/{nbItems}", name="load")
     * @param int $firstItem
     * @param int $nbItems
     * @param TrickRepository $trickRepository
     * @return JsonResponse
     */
    public function loadMore(int $firstItem, int $nbItems, TrickRepository $trickRepository) : JsonResponse
    {
        return $this->json($trickRepository->find5By5($firstItem, $nbItems), '200', [], ['groups' => ['tricks:load', "Default"]]);
    }
}
