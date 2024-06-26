<?php

namespace Bfg\Repository;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Bfg\Repository\Commands\MakeRepositoryCommand;
use Bfg\Repository\Commands\MakeFormulaCommand;

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
            MakeFormulaCommand::class,
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
