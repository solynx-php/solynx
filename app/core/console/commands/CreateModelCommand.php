<?php
namespace app\core\console\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command
{
    protected static $defaultName = 'create:model';

    protected function configure(): void
    {
        $this->setName('create:model')
             ->setDescription('Create a model from stub')
             ->addArgument('name', InputArgument::REQUIRED, 'Model name (e.g. Home or Admin/User)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = str_replace('\\', '/', $input->getArgument('name'));
        $parts = explode('/', $name);
        $class = array_pop($parts);
        if (!str_ends_with($class, 'Model')) {
            $class .= 'Model';
        }

        $sub = implode('/', $parts);
        $base = dirname(__DIR__, 4) . '/app/models';
        $dir = $base . ($sub ? "/{$sub}" : '');
        $file = "{$dir}/{$class}.php";

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_exists($file)) {
            $label = "\033[41;97m ERROR \033[0m";
            $output->writeln("\n{$lable} app/models" . ($sub ? "/{$sub}" : "") . "/{$class}.php already exists.\n");
            return Command::FAILURE;
        }

        $ns  = 'app\\models' . ($sub ? '\\' . str_replace('/', '\\', $sub) : '');
        $use = $sub ? "use app\\models\\Model;" : '';

        $stubPath = dirname(__DIR__, 1) . '/stubs/model.stub';
        if (!file_exists($stubPath)) {
            $label = "\033[41;97m ERROR \033[0m";
            $output->writeln("\n{$label}Missing stub file: stubs/model.stub\n");
            return Command::FAILURE;
        }

        $stub = file_get_contents($stubPath);
        $tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Model', '', $class)));
        $content = str_replace(
            ['{{namespace}}', '{{use}}', '{{class}}', '{{tableName}}'],
            [$ns, $use, $class, "'{$tableName}s'"],
            $stub
        );

        file_put_contents($file, $content);
        $label = "\033[42;97m  CREATE  \033[0m";
        $output->writeln("\n{$label} [app/models" . ($sub ? "/{$sub}" : "") . "/{$class}.php] is successfully created.\n");

        return Command::SUCCESS;
    }
}
