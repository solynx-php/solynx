<?php
namespace app\core\console\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateControllerCommand extends Command
{
    protected static $defaultName = 'create:controller';

    protected function configure()
    {
        $this->setName('create:controller')
             ->setDescription('Create a controller from stub')
             ->addArgument('name', InputArgument::REQUIRED, 'Controller name (e.g. Home or Admin/User)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = str_replace('\\', '/', $input->getArgument('name'));
        $parts = explode('/', $name);
        $class = array_pop($parts);
        if (!str_ends_with($class, 'Controller')) {
            $class .= 'Controller';
        }

        $sub = implode('/', $parts);
        $base = dirname(__DIR__, 4) . '/app/controllers';
        $dir = $base . ($sub ? "/{$sub}" : '');
        $file = "{$dir}/{$class}.php";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_exists($file)) {
            $label = "\033[41;97m ERROR \033[0m";
            $output->writeln("\n{$lable} app/controllers" . ($sub ? "/{$sub}" : "") . "/{$class}.php already exists.\n");
            return Command::FAILURE;
        }

        $ns  = 'app\\controllers' . ($sub ? '\\' . str_replace('/', '\\', $sub) : '');
        $use = $sub ? "use app\\controllers\\Controller;" : '';

        $stubPath = dirname(__DIR__, 1) . '/stubs/controller.stub';
        if (!file_exists($stubPath)) {
            $label = "\033[41;97m ERROR \033[0m";
            $output->writeln("\n{$label}Missing stub file: stubs/controller.stub\n");
            return Command::FAILURE;
        }

        $stub = file_get_contents($stubPath);
        $content = str_replace(
            ['{{namespace}}', '{{use}}', '{{class}}'],
            [$ns, $use, $class],
            $stub
        );

        file_put_contents($file, $content);
        $label = "\033[42;97m  CREATE  \033[0m";
        $output->writeln("\n{$label} [app/controllers" . ($sub ? "/{$sub}" : "") . "/{$class}.php] is successfully created.\n");

        return Command::SUCCESS;
    }
}
