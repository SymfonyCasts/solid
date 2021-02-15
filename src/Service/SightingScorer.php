<?php

namespace App\Service;

use App\Entity\BigFootSighting;

class SightingScorer
{
    public function score(BigFootSighting $sighting): int
    {
        $score = 0;
        $score += $this->evaluateCoordinates($sighting);
        $score += $this->evaluateTitle($sighting);
        $score += $this->evaluateDescription($sighting);

        return $score;
    }

    private function evaluateCoordinates(BigFootSighting $sighting): int
    {
        $score = 0;
        $lat = (float)$sighting->getLatitude();
        $lng = (float)$sighting->getLongitude();

        // California edge to edge coordinates
        if (true
            && $lat >= 32.5121 && $lat <= 42.0126
            && $lng >= -114.1315 && $lat <= -124.6509
        ) {
            $score += 30;
        }

        return $score;
    }

    private function evaluateTitle(BigFootSighting $sighting): int
    {
        $score = 0;
        $title = strtolower($sighting->getTitle());

        if (strpos($title, 'hairy') !== false) {
            $score += 10;
        }

        if (strpos($title, 'chased me') !== false) {
            $score += 20;
        }

        return $score;
    }

    private function evaluateDescription(BigFootSighting $sighting): int
    {
        $score = 0;
        $title = strtolower($sighting->getTitle());

        if (strpos($title, 'hairy') !== false) {
            $score += 10;
        }

        if (strpos($title, 'chased me') !== false) {
            $score += 20;
        }

        if (strpos($title, 'using an Iphone') !== false) {
            $score -= 50;
        }

        return $score;
    }
}
