<?php

declare(strict_types=1);

namespace Locastic\SymfonyTranslationBundle\Cli;

use Doctrine\Migrations\Finder\GlobFinder;
use Locastic\SymfonyTranslationBundle\TranslationMigration\ExecutorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'locastic:symfony-translation:migration:migrate'
)]
final class ExecuteMigrationCommand extends Command
{
    protected OutputInterface $output;

    public function __construct(
        private readonly GlobFinder $translationFinder,
        private readonly string $translationMigrationDirectory,
        private readonly ExecutorInterface $migrationExecutor,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Execute migration of translations');

        $this->addOption(
            'resync',
            'r',
            InputOption::VALUE_NONE,
            'Replay all migrations to resync missed ones without replacing existing ones'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->writeln('Starting to execute migration of translations', OutputInterface::VERBOSITY_NORMAL);

        $migrations = $this->translationFinder->findMigrations($this->translationMigrationDirectory);
        foreach ($migrations as $value) {
            $this->writeLn(\sprintf('Working with Migration "%s', $value), OutputInterface::VERBOSITY_VERBOSE);
            $migration = new $value($this->migrationExecutor);
            $resync = $input->getOption('resync');

            $this->migrationExecutor->execute($migration, $resync);
            $this->migrationExecutor->clearTranslations();
            usleep(100);
        }

        return 0;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        parent::initialize($input, $output);

        $this->output = $output;
    }

    protected function writeLn(string $message, int $level = OutputInterface::OUTPUT_NORMAL): void
    {
        $this->output->writeln(\sprintf('[%s] %s', date('Y-m-d H:i:s'), $message), $level);
    }
}
