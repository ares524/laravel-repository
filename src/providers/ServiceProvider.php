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
        $this->publishes([
            __DIR__ . '/../config/repository.php' => config_path('repository.php')
        ], 'config');
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