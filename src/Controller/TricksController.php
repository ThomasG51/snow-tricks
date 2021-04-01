<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Handler\CreateCommentHandler;
use App\Handler\CreateTrickHandler;
use App\Handler\EditTrickHandler;
use App\Repository\TrickRepository;
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
     * @Route("/show/{slug}", name="show_tricks")
     * @param $slug
     * @param TrickRepository $trickRepository
     * @param Request $request
     * @return Response
     */
    public function show($slug, TrickRepository $trickRepository, Request $request, CreateCommentHandler $commentHandler): Response
    {
        $trick = $trickRepository->findOneBySlug($slug);
        $comment = new Comment();

        if($commentHandler->processing($request,$comment, $trick))
        {
            return $this->redirectToRoute('show_tricks', ['slug' =>$slug]);
        }

        return $this->render('tricks/show.html.twig', [
            'trick' => $trick,
            'formComment' => $commentHandler->form()->createView()
        ]);
    }


    /**
     * Create new tricks
     *
     * @Route("/trick/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param CreateTrickHandler $trickHandler
     * @return Response
     */
    public function create(Request $request, CreateTrickHandler $trickHandler): Response
    {
        $trick = new Trick();

        if ($trickHandler->processing($request, $trick))
        {
            return $this->redirectToRoute('show_tricks', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('tricks/create.html.twig', [
            'formTricks' => $trickHandler->form()->createView()
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
     * @Route("trick/edit/{slug}", name="trick_edit")
     * @param Trick $trick
     * @param Request $request
     * @param EditTrickHandler $trickHandler
     * @return Response
     */
    public function edit(Trick $trick, Request $request, EditTrickHandler $trickHandler): Response
    {
        $this->denyAccessUnlessGranted('CAN_EDIT', $trick, 'Vous ne pouvez pas modifier le trick d\'un autre utilisateur.');

        if($trickHandler->processing($request, $trick))
        {
            return $this->redirectToRoute('show_tricks', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/create.html.twig', [
            'trick' => $trick,
            'formTricks' => $trickHandler->form()->createView()
        ]);
    }
}
