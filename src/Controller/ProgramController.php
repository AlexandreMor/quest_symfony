<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use App\Entity\Program;

use App\Entity\Season;

use App\Entity\Episode;

use App\Service\Slugify;

use Symfony\Component\HttpFoundation\Request;

use App\Form\ProgramType;

use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\Mime\Email;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**

 * @Route("/programs", name="program_")

 */
class ProgramController extends AbstractController

{
    /**

     * @Route("/", name="index")

     */
    public function index(): Response

    {
        $programs = $this->getDoctrine()

            ->getRepository(Program::class)

            ->findAll();

        return $this->render('/program/index.html.twig', [

            'website' => 'Wild Séries',
            'programs' => $programs
        ]);
    }

    /**

     * The controller for the category add form

     *

     * @Route("/new", name="new")

     */
    public function new(Request $request, Slugify $slugify, MailerInterface $mailer): Response

    {

        // Create a new Program Object

        $program = new Program();

        // Create the associated Form

        $form = $this->createForm(ProgramType::class, $program);

        // Get data from HTTP request

        $form->handleRequest($request);

        // Was the form submitted ?

        if ($form->isSubmitted() && $form->isValid()) {
            // Deal with the submitted data

            // Get the Entity Manager

            $entityManager = $this->getDoctrine()->getManager();

            //Slugify

            $slug = $slugify->generate($program->getTitle());

            $program->setSlug($slug);

            $program->setOwner($this->getUser());

            // Persist Program Object

            $entityManager->persist($program);

            // Flush the persisted object

            $entityManager->flush();

            // Envoi de mail

            $email = (new Email())

                ->from($this->getParameter('mailer_from'))

                ->to('98c13e05dd-9e6289@inbox.mailtrap.io')

                ->subject('Une nouvelle série vient d\'être publiée !')

                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);

            // Finally redirect to categories list

            return $this->redirectToRoute('program_index');
        }

        // Render the form

        return $this->render('program/new.html.twig', ["form" => $form->createView()]);
    }

    /**

     * Getting a program by id

     *

     * @Route("/show/{slug}", name="show")

     * @return Response

     */

    public function show(Program $program): Response

    {


        $seasons = $this->getDoctrine()

            ->getRepository(Season::class)

            ->findBy(['program' => $program]);

        if (!$program) {

            throw $this->createNotFoundException(

                'No program found in program\'s table.'

            );
        }

        return $this->render('program/show.html.twig', [

            'program' => $program,
            'seasons' => $seasons

        ]);
    }

    /**
     * @Route("/{slug}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Program $program): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        // Check wether the logged in user is the owner of the program

        if (!($this->getUser() == $program->getOwner())) {

            // If not the owner, throws a 403 Access Denied exception

            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('program_index');
        }

        return $this->render('program/edit.html.twig', [
            'program' => $program,
            'form' => $form->createView(),
        ]);
    }

    /**

     * Getting a program by id

     *

     * @Route("/{slug}/seasons/{seasonId<^[0-9]+$>}", name="season_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})

     * @return Response

     */
    public function showSeason(Program $program, Season $season)
    {


        $episodes = $this->getDoctrine()

            ->getRepository(Episode::class)

            ->findBy(['season' => $season]);

        if (!$season) {

            throw $this->createNotFoundException(

                'No season found in season\'s table.'

            );
        }

        return $this->render('program/season_show.html.twig', [

            'program' => $program,
            'season' => $season,
            'episodes' => $episodes

        ]);
    }
    /**
     * @Route("/{slugProg}/seasons/{seasonId<^[0-9]+$>}/episodes/{slugEp}", name="episode_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slugProg": "slug"}})
     * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
     * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"slugEp": "slug"}})
     */
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
        }
        return $this->render('program/episode_show.html.twig', [

            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form->CreateView()

        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Program $program): Response
    {
        // Check wether the logged in user is the owner of the program

        if (!($this->getUser() == $program->getOwner())) {

            // If not the owner, throws a 403 Access Denied exception

            throw new AccessDeniedException('Only the owner can delete the program!');
        }
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index');
    }
}
