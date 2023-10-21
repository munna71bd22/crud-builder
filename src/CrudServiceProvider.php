<?php

namespace SmartApp\Cb;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{

    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     */
    public function registerCommands()
    {
        //register commands
        $this->commands([
            \Smartapp\Cb\Commands\GenerateCrud::class,
        ]);
      
    }
}