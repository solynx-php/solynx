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

        $label = "\033[43;97m  INFO  \033[0m";
        
        if ($host === '0.0.0.0') {
            $ip = $this->getLocalIp();
            $output->writeln("\n{$label} Starting server at http://{$ip}:{$port}\n");
        } else {
            $output->writeln("\n{$label} Starting server at http://{$host}:{$port}\n");
        }

        $cmd = sprintf('php -S %s:%s -t "%s"', $host, $port, $publicDir);
        passthru($cmd);

        return Command::SUCCESS;
    }

    private function getLocalIp(): string
    {
        if (stripos(PHP_OS, 'WIN') === 0) {
            exec('ipconfig', $output);
            foreach ($output as $line) {
                if (preg_match('/IPv4.*?:\s*(\d+\.\d+\.\d+\.\d+)/', $line, $matches)) {
                    $ip = $matches[1];
                    if (!preg_match('/^(127\.|172\.(1[6-9]|2[0-9]|3[01])\.|169\.254\.)/', $ip)) {
                        return $ip;
                    }
                }
            }
        } else {
            $output = shell_exec('hostname -I 2>/dev/null');
            if ($output) {
                $ips = explode(' ', trim($output));
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) 
                        && !preg_match('/^(127\.|172\.(1[6-9]|2[0-9]|3[01])\.|169\.254\.)/', $ip)) {
                        return $ip;
                    }
                }
            }
            
            $output = shell_exec('ifconfig 2>/dev/null || ip addr show 2>/dev/null');
            if ($output && preg_match('/inet\s+(\d+\.\d+\.\d+\.\d+)/', $output, $matches)) {
                $ip = $matches[1];
                if (!preg_match('/^(127\.|172\.(1[6-9]|2[0-9]|3[01])\.|169\.254\.)/', $ip)) {
                    return $ip;
                }
            }
        }

        return getHostByName(getHostName());
    }
}