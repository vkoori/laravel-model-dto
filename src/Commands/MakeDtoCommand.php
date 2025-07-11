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

        $file = "{$path}/{$dtoClass}.php";

        $fields = '';
        $methods = '';

        foreach ($dtoConfig as $column => $columnInfo) {
            $type = $columnInfo['type'];
            $types = explode('|', $type);
            foreach ($types as &$value) {
                $nullable = str_starts_with($value, '?');
                $value = ltrim($value, '?');
                if (!str_starts_with($value, '\\') && class_exists($value)) {
                    $value = '\\' . $value;
                }
                if ($nullable) {
                    $value = '?' . $value;
                }
            }
            $type = implode('|', $types);

            $camel = Str::camel($column);
            $ucfirst = ucfirst($camel);

            // Field
            $fields .= "\n    private {$type} \${$camel};";

            // Setter
            $setter = "set{$ucfirst}";
            $getter = "get{$ucfirst}";

            // Method: setter
            $methods .= "\n
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
    }";
        }

        $stub = str_replace(['DummyNamespace', 'DummyClass', '@fields', '@methods'], [
            $namespace,
            $dtoClass,
            $fields,
            $methods
        ], $stub);

        if (!file_exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        if (File::exists($file)) {
            $this->warn("DTO already exists at: {$file}");
            if (!$this->confirm('Do you want to overwrite?')) {
                return;
            }
        }

        File::put($file, $stub);

        $this->info("DTO created successfully: {$dtoClass} [{$file}]");
    }
}
