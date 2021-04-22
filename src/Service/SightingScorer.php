<?php

namespace App\Service;

use App\Entity\BigFootSighting;
use App\Model\BigFootSightingScore;

class SightingScorer
{
    public function score(BigFootSighting $sighting): BigFootSightingScore
    {
        $score = 0;
        $score += $this->evaluateCoordinates($sighting);
        $score += $this->evaluateTitle($sighting);
        $score += $this->evaluateDescription($sighting);

        return new BigFootSightingScore($score);
    }

    private function evaluateCoordinates(BigFootSighting $sighting): int
    {
        $score = 0;
        $lat = (float)$sighting->getLatitude();
        $lng = (float)$sighting->getLongitude();

        // California edge to edge coordinates
        if ($lat >= 32.5121 && $lat <= 42.0126
            && $lng >= -114.1315 && $lng <= -124.6509
        ) {
            $score += 30;
        }

        return $score;
    }

    private function evaluateTitle(BigFootSighting $sighting): int
    {
        $score = 0;
        $title = strtolower($sighting->getTitle());

        if (stripos($title, 'hairy') !== false) {
            $score += 10;
        }

        if (stripos($title, 'chased me') !== false) {
            $score += 20;
        }

        return $score;
    }

    private function evaluateDescription(BigFootSighting $sighting): int
    {
        $score = 0;
        $title = strtolower($sighting->getDescription());

        if (stripos($title, 'hairy') !== false) {
            $score += 10;
        }

        if (stripos($title, 'chased me') !== false) {
            $score += 20;
        }

        if (stripos($title, 'using an iPhone') !== false) {
            $score -= 50;
        }

        return $score;
    }
}
