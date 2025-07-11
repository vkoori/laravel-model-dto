<?php

namespace Vkoori\EntityDto\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDtoCommand extends Command
{
    protected $signature = 'make:dto {name} {--module=}';
    protected $description = 'Generate a DTO class based on config';

    public function handle()
    {
        $dtoName = $this->argument('name');
        $moduleName = $this->option('module');

        $dtoConfig = Config::get("dto.{$dtoName}");

        if (!$dtoConfig) {
            $this->error("No config found for '{$dtoName}' in dto.php");
            return;
        }

        $dtoClass = "{$dtoName}DTO";
        $stub = File::get(dirname(__FILE__, 2) . '/stubs/dto.stub');

        $namespace = "App\\Models\\DTO";
        $path = "app/Models/DTO";

        if ($moduleName) {
            $namespace = "Modules\\{$moduleName}\\Models\\DTO";
            $path = "Modules/{$moduleName}/app/Models";
        }

        $fields = '';
        $methods = '';

        foreach ($dtoConfig as $column => $type) {
            $camel = Str::camel($column);
            $ucfirst = ucfirst($camel);

            // Field
            $fields .= "private {$type} \${$camel};\n    ";

            // Setter
            $setter = "set{$ucfirst}";
            $getter = "get{$ucfirst}";

            // Method: setter
            $methods .= "
    public function {$setter}({$type} \${$camel}): static
    {
        \$this->{$camel} = \${$camel};
        \$this->markSet('{$column}');

        return \$this;
    }\n";

            // Method: getter
            $methods .= "
    public function {$getter}(): {$type}
    {
        return \$this->{$camel};
    }\n";
        }

        $stub = str_replace(['DummyNamespace', 'DummyClass', '@fields', '@methods'], [
            $namespace,
            $dtoClass,
            $fields,
            $methods
        ], $stub);

        if (File::exists($path)) {
            $this->warn("DTO already exists at: {$path}");
            if (!$this->confirm('Do you want to overwrite?')) {
                return;
            }
        }

        File::put($path, $stub);

        $this->info("DTO created successfully: {$dtoClass}");
    }
}
