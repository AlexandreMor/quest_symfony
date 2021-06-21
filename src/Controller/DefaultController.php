<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use App\Entity\Program;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController

{
    /**

     * @Route("/", name="app_index")

     */
    public function index(Request $request)
    {
        $programs = $this->getDoctrine()

            ->getRepository(Program::class)

            ->findAll();

        return $this->render('/index.html.twig', ['programs' => $programs]);
    }

    /**

     * @Route("/my-profile", name="app_profile")

     */
    public function myProfil()
    {
        $user = $this->getUser();
        return $this->render('/profil.html.twig', ['user' => $user]);
    }
}
