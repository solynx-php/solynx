<?php
namespace app\core\console;

use Symfony\Component\Console\Application as ConsoleApp;
use app\core\console\commands\RunServerCommand;
use app\core\console\commands\CreateControllerCommand;
use app\core\console\commands\CreateMigrationCommand;
use app\core\console\commands\CreateModelCommand;
use app\core\console\commands\MigrateCommand;
use app\core\console\commands\MigrateFreshCommand;
use app\core\console\commands\MigrateStatusCommand;

class Kernel
{
    protected ConsoleApp $console;

    public function __construct()
    {
        $this->console = new ConsoleApp('Drift Console', '1.0.0');
        $this->register();
    }

    protected function register(): void
    {
        $this->console->add(new RunServerCommand());
        $this->console->add(new CreateControllerCommand());
        $this->console->add(new CreateModelCommand());
        $this->console->add(new MigrateCommand());
        $this->console->add(new CreateMigrationCommand());
        $this->console->add(new MigrateStatusCommand());
        $this->console->add(new MigrateFreshCommand());
    }

    public function handle($input, $output)
    {
        return $this->console->run($input, $output);
    }

    public function terminate($input, $status): void {}
}
