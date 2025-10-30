<?php

namespace app\core\console\commands;

use app\core\Database;
use app\core\Database\Migration;
use app\core\Database\MigrationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use ReflectionClass;

class MigrateFreshCommand extends Command
{
    protected static $defaultName = 'migrate:fresh';

    protected function configure()
    {
        $this->setName('migrate:fresh')
             ->setDescription('Drop all tables and re-run all migrations from scratch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base = dirname(__DIR__, 4);
        require_once "{$base}/bootstrap/app.php";

        $db = Database::connect();
        $repo = new MigrationRepository($db);
        $path = "{$base}/app/databases/migrations";

        // Confirm
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            "\n<question>This will DROP all tables. Continue? (Y/n)</question> \n",
            false
        );
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln("<comment>Operation cancelled.</comment>\n");
            return Command::SUCCESS;
        }

        // Disable foreign key checks (for MySQL)
        $db->exec("SET FOREIGN_KEY_CHECKS=0");

        // Drop all existing tables
        $tables = $db->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            $db->exec("DROP TABLE IF EXISTS `$table`");
        }

        $db->exec("SET FOREIGN_KEY_CHECKS=1");
        $output->writeln("\n<info>All tables dropped.</info>\n");

        // Recreate migrations table
        $repo = new MigrationRepository($db);
        $output->writeln("<info>Running fresh migrations...</info>\n");

        $files = glob($path . '/*.php');
        if (empty($files)) {
            $output->writeln("\n<comment>No migration files found.</comment>\n");
            return Command::SUCCESS;
        }

        $batch = 1;
        foreach ($files as $file) {
            $fileName = basename($file);

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
                    $label = "\033[42;97m  MIGRATED  \033[0m";
                    $output->writeln("{$label} {$fileName}");
                }
            }
        }

        $output->writeln("\n<info>Database refreshed successfully.</info>\n");
        return Command::SUCCESS;
    }
}
