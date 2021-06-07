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
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {

        return $this->render('program/episode_show.html.twig', [

            'program' => $program,
            'season' => $season,
            'episode' => $episode

        ]);
    }
}
