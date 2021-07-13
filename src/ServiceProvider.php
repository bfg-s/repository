<?php

namespace Bfg\Repository;

use Bfg\Installer\Providers\InstalledProvider;
use Bfg\Repository\Commands\MakeRepositoryCommand;

/**
 * Class ServiceProvider
 * @package Bfg\Repository
 */
class ServiceProvider extends InstalledProvider
{
    /**
     * The name of extension.
     * @var string|null
     */
    public ?string $name = "bfg/repository";

    /**
     * The description of extension.
     * @var string|null
     */
    public ?string $description = "Missing repositories for Eloquent models Laravel.";

    /**
     * Set as installed by default.
     * @var bool
     */
    public bool $installed = true;

    /**
     * Executed when the provider is registered
     * and the extension is installed.
     * @return void
     */
    public function installed(): void
    {
        $this->commands([
            MakeRepositoryCommand::class
        ]);
    }

    /**
     * Executed when the provider run method
     * "boot" and the extension is installed.
     * @return void
     */
    public function run(): void
    {
        //
    }
}

