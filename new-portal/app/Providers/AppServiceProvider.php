<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Nationalities;
use App\Genders;
use App\MaritalStatus;

class AppServiceProvider extends ServiceProvider
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
        view()->share('nationalities', Nationalities::all());
        view()->share('gender', Genders::all());
        view()->share('marital_status', MaritalStatus::all());
    }
}
