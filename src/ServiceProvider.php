<?php

namespace Devhereco\CustomChart;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        // ...
    }

    public function register()
    {
        $this->app->singleton(CustomChart::class, function () {
            return new CustomChart();
        });
    }

}
