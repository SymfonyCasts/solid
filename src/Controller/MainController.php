<?php

namespace App\Controller;

use App\Entity\BigFootSighting;
use App\Repository\BigFootSightingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(BigFootSightingRepository $bigFootSightingRepository)
    {
        $sightings = $bigFootSightingRepository->findLatest(25);

        return $this->render('main/homepage.html.twig', [
            'sightings' => $sightings
        ]);
    }

    /**
     * @Route("/about", name="app_about")
     */
    public function about()
    {
        return $this->render('main/about.html.twig');
    }
}
