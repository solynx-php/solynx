<?php
namespace app\core\console;

use Symfony\Component\Console\Application as ConsoleApp;
use app\core\console\commands\RunServerCommand;
use app\core\console\commands\MakeControllerCommand;
use app\core\console\commands\MakeModelCommand;

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
        $this->console->add(new MakeControllerCommand());
        $this->console->add(new MakeModelCommand());
    }

    public function handle($input, $output)
    {
        return $this->console->run($input, $output);
    }

    public function terminate($input, $status): void {}
}
