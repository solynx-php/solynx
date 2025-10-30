<?php
namespace app\core\console\commands;

use app\core\Database;
use app\core\Database\MigrationRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class MigrateStatusCommand extends Command
{
    protected static $defaultName = 'migrate:status';

    protected function configure()
    {
        $this->setName('migrate:status')
             ->setDescription('Show migration status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $base = dirname(__DIR__, 4);
        require_once "{$base}/bootstrap/app.php";

        $db = Database::connect();
        $repo = new MigrationRepository($db);
        $path = "{$base}/app/databases/migrations";
        $allFiles = array_map('basename', glob($path . '/*.php'));

        $ran = $repo->all();
        $ranNames = array_column($ran, 'migration');

        $rows = [];
        foreach ($allFiles as $file) {
            $isRan = in_array($file, $ranNames, true);
            $batch = '';
            $migratedAt = '';

            if ($isRan) {
                $entry = current(array_filter($ran, fn($r) => $r['migration'] === $file));
                $batch = $entry['batch'];
                $migratedAt = $entry['migrated_at'];
            }

            $rows[] = [
                $isRan ? 'Yes' : 'No',
                $file,
                $batch,
                $migratedAt,
            ];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['Ran', 'Migration', 'Batch', 'Migrated At'])
            ->setRows($rows)
            ->render();

        return Command::SUCCESS;
    }
}
