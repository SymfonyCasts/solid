<?php

namespace App\Model;

class BigFootSightingScore
{
    private int $score;

    public function __construct(int $score)
    {
        $this->score = $score;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getQualityLevel(): string
    {
        switch (true) {
            case $this->score <= 30:
                return 'low';
            case $this->score <= 60:
                return 'medium';
            default:
                return 'high';
        }
    }
}
