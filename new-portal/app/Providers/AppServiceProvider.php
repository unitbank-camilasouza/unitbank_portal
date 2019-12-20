<?php

namespace App\Providers;

use App\Contracts;
use App\CoWalletsJunctions;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Nationalities;
use App\Genders;
use App\MaritalStatus;
use App\Wallets;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     *
     */
    protected $bindsId = [
        'yield' => \App\Yields::class,
        'customer' => \App\Customers::class,
        'withdraw' => \App\Withdrawals::class,
        'contract' => \App\Contracts::class,
    ];

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
        // views information share
        view()->share('nationalities', Nationalities::all());
        view()->share('gender', Genders::all());
        view()->share('marital_status', MaritalStatus::all());


        // loop to define all routing bindings
        foreach ($this->bindsId as $bind_key_id => $bind_class) {
            Route::bind($bind_key_id, function ($value) use ($bind_class) {
                try {
                    $id = decrypt($value);
                    return $bind_class::findOrFail($id);
                } catch (DecryptException $e) {
                    return abort(404);
                }
            });
        }

        Gate::define('request-contract-details', function ($contract) {
            dd($contract);

            if(auth('customer')->check()) {
                $co_wallet = CoWalletsJunctions::where('id_wallet', $contract->id_wallet)
                                             ->firstOrFail();

                return auth('customer')->id() === $co_wallet->id_customer;
            }

            return true;
        });

        Gate::define('request-withdraw-details', function ($withdraw) {
            // TODO: defines the withdraw policy
        });

        Gate::define('request-yield-details', function ($yield) {
            // TODO: defines the yield policy
        });

        Gate::define('request-user-details', function ($user) {
            // TODO: defines the user policy
        });
    }
}
