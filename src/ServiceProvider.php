<?php

namespace Bfg\Repository;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Bfg\Repository\Commands\MakeRepositoryCommand;

/**
 * Class ServiceProvider.
 * @package Bfg\Repository
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Executed when the provider is registered
     * and the extension is installed.
     * @return void
     */
    public function register(): void
    {
        $this->commands([
            MakeRepositoryCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     * @return void
     */
    public function boot()
    {
        //
    }
}
