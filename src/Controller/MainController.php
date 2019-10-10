<?php

namespace App\Controller;

use App\Entity\BigFootSighting;
use App\GitHub\GitHubApiHelper;
use App\Repository\BigFootSightingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(BigFootSightingRepository $bigFootSightingRepository, Request $request)
    {
        $sightings = $bigFootSightingRepository->findLatest(25);

        return $this->render('main/homepage.html.twig', [
            'sightings' => $sightings
        ]);
    }

    /**
     * @Route("/_sightings", name="app_sightings_partial_list")
     */
    public function loadSightingsPartial(BigFootSightingRepository $bigFootSightingRepository, Request $request)
    {
        // simple pagination!
        $page = $request->query->get('page', 1);
        $limit = 25;
        $offset = max(0, ($page - 1) * $limit);
        $sightings = $bigFootSightingRepository->findLatest($limit, $offset);

        $html = $this->renderView('main/_sightings.html.twig', [
            'sightings' => $sightings
        ]);

        $data = [
            'html' => $html,
            'next' => count($sightings) > 0 ? ++$page : null,
        ];

        return $this->json($data);
    }

    /**
     * @Route("/api/github-organization", name="app_github_organization_info")
     */
    public function gitHubOrganizationInfo(GitHubApiHelper $apiHelper)
    {
        $organizationName = 'SymfonyCasts';
        $organization = $apiHelper->getOrganizationInfo($organizationName);
        $repositories = $apiHelper->getOrganizationRepositories($organizationName);

        return $this->json([
            'organization' => $organization,
            'repositories' => $repositories,
        ]);
    }

    /**
     * @Route("/sighting/{id}", name="app_sighting_show")
     */
    public function showSighting(BigFootSighting $bigFootSighting)
    {
        return $this->render('main/sighting_show.html.twig', [
            'sighting' => $bigFootSighting
        ]);
    }
}
