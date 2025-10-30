<?php

namespace app\core\console\commands;

use app\core\Database;
use app\core\Database\Migration;
use app\core\Database\MigrationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ReflectionClass;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setName('migrate')
            ->setDescription('Run all pending database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = dirname(__DIR__, 4);
        require_once "{$base}/bootstrap/app.php";

        $db = Database::connect();
        $repo = new MigrationRepository($db);
        $path = "{$base}/app/databases/migrations";

        $files = glob($path . '/*.php');
        if (empty($files)) {
            $output->writeln("\n<comment>No migration files found.</comment>\n");
            return Command::SUCCESS;
        }

        $batch = $repo->latestBatch() + 1;
        $newMigrations = 0;
        $totalFiles = count($files);
        $migratedFiles = 0;

        foreach ($files as $file) {
            $fileName = basename($file);

            if ($repo->isMigrated($fileName)) {
                $migratedFiles++;
                continue;
            }

            $before = get_declared_classes();
            require_once $file;
            $after = get_declared_classes();
            $newClasses = array_diff($after, $before);

            foreach ($newClasses as $class) {
                $ref = new ReflectionClass($class);
                if ($ref->isAbstract() || !$ref->isSubclassOf(Migration::class)) {
                    continue;
                }

                $migration = new $class($db);
                if (method_exists($migration, 'up')) {
                    $migration->up();
                    $repo->log($fileName, $batch);
                    $newMigrations++;
                    $label = "\033[42;97m  MIGRATED  \033[0m";
                    $output->writeln("\n{$label} {$fileName}\n");
                } else {
                    $output->writeln("\n<error>Class {$class} has no up() method</error>\n");
                }
            }
        }

        if ($newMigrations === 0 && $migratedFiles === $totalFiles) {
            $output->writeln("\n<comment>All migrations are already up to date.</comment>\n");
        }

        return Command::SUCCESS;
    }
}
