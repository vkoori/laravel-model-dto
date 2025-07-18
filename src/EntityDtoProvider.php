<?php

namespace Vkoori\EntityDto;

use Illuminate\Support\ServiceProvider;
use Vkoori\EntityDto\Commands\MakeDtoCommand;

class EntityDtoProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            MakeDtoCommand::class,
        ]);
    }
}
