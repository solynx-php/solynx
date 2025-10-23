<?php

namespace app\core\console\commands;

use app\core\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionClass;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure(): void
    {
        $this->setName('migrate')
            ->setDescription('Run all database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = dirname(__DIR__, 4);
        require_once "{$base}/bootstrap/app.php";

        $db = Database::connect();
        $path = "{$base}/app/databases/migrations";

        $files = glob($path . '/*.php');
        if (empty($files)) {
            $output->writeln("<comment>No migration files found.</comment>");
            return Command::SUCCESS;
        }

        foreach ($files as $file) {
            $before = get_declared_classes();
            require_once $file;
            $after = get_declared_classes();

            $newClasses = array_diff($after, $before);

            if (empty($newClasses)) {
                $output->writeln("<error>No class found in " . basename($file) . "</error>");
                continue;
            }

            foreach ($newClasses as $class) {
                $ref = new ReflectionClass($class);

                // skip abstract classes and classes outside migrations
                if ($ref->isAbstract() || !$ref->isSubclassOf(\app\core\Database\Migration::class)) {
                    continue;
                }

                $migration = new $class($db);
                if (method_exists($migration, 'up')) {
                    $migration->up($db);
                    $label = "\033[42;97m  MIGRATED  \033[0m";
                    $output->writeln("\n{$label} " . basename($file));
                } else {
                    $output->writeln("<error>Class {$class} has no up() method</error>");
                }
            }
        }

        return Command::SUCCESS;
    }
}
