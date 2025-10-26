<?php
namespace app\core\console\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMigrationCommand extends Command {
    protected static $defaultName = 'create:migration';

    protected function configure(): void {
        $this->setName(self::$defaultName)
             ->setDescription('Create a new migration file')
             ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $name = $input->getArgument('name');
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";
        $path = dirname(__DIR__, 3) . '/databases/migrations/' . $fileName;

        $className = $this->generateClassName($name);
        $table = $this->inferTableName($name);

        $stubPath = dirname(__DIR__) . '/stubs/migration.stub';
        $stub = file_get_contents($stubPath);

        $content = str_replace(
            ['{{ class }}', '{{ table }}'],
            [$className, $table],
            $stub
        );

        file_put_contents($path, $content);
        $label = "\033[42;97m  CREATED  \033[0m";
        $output->writeln("\n{$label} {$fileName} is created successfully.");
        return Command::SUCCESS;
    }

    private function generateClassName(string $name): string {
        $parts = explode('_', $name);
        $parts = array_map('ucfirst', $parts);
        return implode('', $parts);
    }

    private function inferTableName(string $name): string {
        if (preg_match('/create_(.*?)_table/', $name, $m)) {
            return $m[1];
        }
        return $name;
    }
}
