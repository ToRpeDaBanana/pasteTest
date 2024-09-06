<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;

#[AsCommand(
    name: 'CleanupPastesCommand',
    description: 'Delete old paste',
)]
class CleanupPastesCommand extends Command
{
    protected static $defaultName = 'app:cleanup-pastes';
    private PasteCleanupService $cleanupService;

    public function __construct(PasteCleanupService $cleanupService)
    {
        parent::__construct();
        $this->cleanupService = $cleanupService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cleanupService->cleanupExpiredPastes();
        $output->writeln('Expired pastes cleaned up successfully.');
        
        return Command::SUCCESS;
    }
}
