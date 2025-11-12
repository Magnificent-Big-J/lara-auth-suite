<?php

namespace Rainwaves\LaraAuthSuite\Console;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateSwaggerDocsCommand extends Command
{
    protected $signature = 'authsuite:generate-docs';

    protected $description = 'Generate OpenAPI (Swagger) documentation for Lara Auth Suite';

    public function handle(): int
    {
        $this->info('Generating Swagger documentation...');

        $output = base_path('build/openapi.json');
        $openapi = Generator::scan([__DIR__.'/../../']);

        file_put_contents($output, $openapi->toJson());
        $this->info("✅ Documentation generated: {$output}");

        return self::SUCCESS;
    }
}
