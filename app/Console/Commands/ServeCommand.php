<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'serve')]
class ServeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Laravel development server...');

        $host = $this->option('host') ?: '127.0.0.1';
        $port = $this->option('port') ?: '8000';

        $this->info("Laravel development server started on http://{$host}:{$port}");
        $this->info('Press Ctrl+C to stop the server');

        $publicPath = $this->laravel->basePath('public');
        
        $process = new Process([PHP_BINARY, '-S', "{$host}:{$port}", '-t', $publicPath]);
        $process->setTimeout(null);
        
        // Vérifier si on est sur Windows avant d'activer le mode TTY
        if (DIRECTORY_SEPARATOR !== '\\') {
            $process->setTty(true);
        }
        
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        return 0;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The host address to serve the application on', '127.0.0.1'],
            ['port', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'The port to serve the application on', 8000],
        ];
    }
}
