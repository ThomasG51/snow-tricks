<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show($id, TrickRepository $trickRepository, Request $request): Response
    {
        $trick = $trickRepository->find($id);
        $comment = new Comment();

        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('show_tricks', [
                'id' =>$id
            ]);
        }

        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'formComment' => $formComment->createView()
        ]);
    }


    /**
     * Create new tricks
     *
     * @Route("/trick/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request): Response
    {
        $trick = new Trick();
        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if ($formTrick->isSubmitted() && $formTrick->isValid())
        {
            $trick->setCreatedAt(new \DateTime());
            $trick->setUser($this->getUser());

            foreach($trick->getVideos() as $video)
            {
                // Pattern for youtube url
                preg_match('/^(\w+:\/\/\w+\.\w+\.*\w+\/(\w+\?v=)*)(\w+)((\&\S+)*)$/',$video->getUrl(), $videoId);
                $video->setUrl($videoId[3]);
            }

            $this->getDoctrine()->getManager()->persist($trick);
            $this->getDoctrine()->getManager()->flush();

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
        $view = $this->renderView('tricks/_load_tricks.html.twig', [
            'tricks' => $trickRepository->find5By5($firstItem, $nbItems)
        ]);

        return $this->json($view, '200');
    }


    /**
     * Delete trick
     *
     * @Route("trick/delete/{id}", name="trick_delete")
     * @param Request $request
     * @param Trick $trick
     * @param EntityManagerInterface $manager
     * @return Response
     * @throws \Exception
     */
    public function delete(Request $request, Trick $trick): Response
    {
        $this->denyAccessUnlessGranted('CAN_DELETE', $trick, 'Vous ne pouvez pas supprimer le trick d\'un autre utilisateur.');

        if($this->isCsrfTokenValid('delete_trick_'.$trick->getId(), $request->request->get('token')))
        {
            $this->getDoctrine()->getManager()->remove($trick);
            $this->getDoctrine()->getManager()->flush();

            foreach($trick->getMedia() as $media)
            {
                unlink('upload/tricks/'.$media->getName());
            }

            return $this->redirectToRoute('home');
        }

        throw new \Exception('Delete trick failed', 400);
    }


    /**
     * Upload media with dropzone
     *
     * @Route("/upload", name="upload")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function uploadMedia(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        if(!$file->move('upload/tricks', $file->getClientOriginalName()))
        {
            throw new \Exception('L\'upload n\'a pas pu être effectué');
        }

        return $this->json('Upload effectué');
    }


    /**
     * Edit trick
     *
     * @Route("trick/edit/{id}", name="trick_edit")
     * @param Trick $trick
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Trick $trick, Request $request): Response
    {
        $this->denyAccessUnlessGranted('CAN_EDIT', $trick, 'Vous ne pouvez pas modifier le trick d\'un autre utilisateur.');

        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid())
        {
            $trick->setModifiedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('tricks/create.html.twig', [
            'trick' => $trick,
            'formTricks' => $formTrick->createView()
        ]);
    }
}
