<?php

namespace App\Command;

use App\Repository\BigFootSightingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateSightingScoresCommand extends Command
{
    protected static $defaultName = 'app:update-sighting-scores';

    private $bigFootSightingRepository;
    private $entityManager;

    public function __construct(BigFootSightingRepository $bigFootSightingRepository, EntityManagerInterface $entityManager)
    {
        $this->bigFootSightingRepository = $bigFootSightingRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Update the "score" for a sighting')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $sightings = $this->bigFootSightingRepository->findAll();
        $io->progressStart(count($sightings));
        foreach ($sightings as $sighting) {
            $io->progressAdvance();
            $characterCount = 0;
            foreach ($sighting->getComments() as $comment) {
                $characterCount += strlen($comment->getContent());
            }

            $score = ceil(min($characterCount / 500, 10));
            $sighting->setScore($score);
            $this->entityManager->flush();
        }
        $io->progressFinish();
    }
}
