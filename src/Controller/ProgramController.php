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

 * Getting a program by id

 *

 * @Route("/show/{id<^[0-9]+$>}", name="show")

 * @return Response

 */

public function show(Program $program):Response

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

 * @Route("/{programId<^[0-9]+$>}/seasons/{seasonId<^[0-9]+$>}", name="season_show")
 * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
 * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})

 * @return Response

 */
public function showSeason(Program $program, Season $season) {


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
 * @Route("/{programId<^[0-9]+$>}/seasons/{seasonId<^[0-9]+$>}/episodes/{episodeId<^[0-9]+$>}", name="episode_show")
 * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"programId": "id"}})
 * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
 * @ParamConverter("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "id"}})
 */
public function showEpisode(Program $program, Season $season, Episode $episode) {

    return $this->render('program/episode_show.html.twig', [

        'program' => $program,
        'season' => $season,
        'episode' => $episode

    ]);
}
}