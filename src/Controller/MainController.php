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
        $sightings = $bigFootSightingRepository->findLatest();

        return $this->render('main/homepage.html.twig', [
            'sightings' => $sightings
        ]);
    }

    /**
     * @Route("/sighting/{id}", name="app_sighting_show")
     */
    public function showSighting(BigFootSighting $bigFootSighting)
    {
        return $this->render('main/sighting_show.html.twig', [
            'slighting' => $bigFootSighting
        ]);
    }
}
