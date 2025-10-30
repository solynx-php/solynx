<?php
namespace app\core\console\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunServerCommand extends Command
{
    protected static $defaultName = 'run';

    protected function configure()
    {
        $this->setName('run')
             ->setDescription('Start PHP development server')
             ->addArgument('host', InputArgument::OPTIONAL, 'Host:Port', '127.0.0.1:8000');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$host, $port] = explode(':', $input->getArgument('host') . ':8000');
        $publicDir = dirname(__DIR__, 4) . '/public';

        if (!is_dir($publicDir)) {
            $label = "\033[41;97m ERROR \033[0m";
            $output->writeln("\n{$label}Public directory not found at {$publicDir}\n");
            return Command::FAILURE;
        }

        $ip = getHostByName(getHostName());
        if ($host === '0.0.0.0') {
            $host = $ip;
            $label= "\033[43;97m  INFO  \033[0m";
            $output->writeln("\n{$label} Starting server at http://{$host}:{$port}\n");
        } else {
            $output->writeln("\n{$label}Starting server at http://{$host}:{$port}\n");
        }

        $cmd = sprintf('php -S %s:%s -t "%s"', $host, $port, $publicDir);
        passthru($cmd);

        return Command::SUCCESS;
    }
}
