<?php

namespace Flaravel\Generator;

use Flaravel\Generator\Commands\FlaravelGenerator;
use Illuminate\Support\ServiceProvider;

class FlaravelGenertorProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FlaravelGenerator::class,
            ]);
        }
    }
}
