<?php

namespace Daria\JWT;

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
