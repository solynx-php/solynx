<?php
namespace app\core\console\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command {
    protected static $defaultName = 'migrate';

    protected function configure(): void
    {
        $this->setName('migrate')
             ->setDescription('Migrate the database')
            ->addArgument('name', InputArgument::OPTIONAL, 'Migration name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $db = new \PDO('mysql:host=127.0.0.1;dbname=app', 'root', '');
        $path = dirname(__DIR__, 3) . '/databases/migrations';

        foreach (glob($path . '/*.php') as $file) {
            $migration = require $file;
            $migration->up();
            $label = "\033[42;97m  Migrated  \033[0m";
            $output->writeln("{$label} " . basename($file));
        }

        return Command::SUCCESS;
    }
}
