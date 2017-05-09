<?php

namespace Leantony\Database\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Leantony\Database\Commands\GenerateRepository;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadHelpers();

        $this->publishes([
            __DIR__ . '/../config/repository.php' => config_path('repository.php')
        ], 'config');
    }

    /**
     * Load helper function files
     */
    protected function loadHelpers()
    {
        $files = glob(__DIR__ . '/../helpers/*.php');
        foreach ($files as $file) {
            require_once($file);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(GenerateRepository::class);
    }
}