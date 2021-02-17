<?php

namespace App\Command;

use App\Repository\BigFootSightingRepository;
use App\Service\DebuggableSightingScorer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DebugBigFootSightingScoreCommand extends Command
{
    protected static $defaultName = 'app:sighting:debug';

    private $debuggableSightingScorer;
    private $bigFootSightingRepository;

    public function __construct(DebuggableSightingScorer $debuggableSightingScorer, BigFootSightingRepository $bigFootSightingRepository)
    {
        parent::__construct();

        $this->debuggableSightingScorer = $debuggableSightingScorer;
        $this->bigFootSightingRepository = $bigFootSightingRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Computes the score of the given record and prints the time it took to be scored')
            ->addArgument('id', InputArgument::REQUIRED, 'BigFootSighting id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $sighting = $this->bigFootSightingRepository->find($id);

        if (!$sighting) {
            $io->error('BigFootSighting record not found');
            return Command::FAILURE;
        }

        $bfsScore = $this->debuggableSightingScorer->score($sighting);

        $io->writeln(sprintf('Score: %d', $bfsScore->getScore()));
        $io->writeln(sprintf('Time spent calculating score: %f seconds', $bfsScore->getCalculationTime()));

        return Command::SUCCESS;
    }
}
