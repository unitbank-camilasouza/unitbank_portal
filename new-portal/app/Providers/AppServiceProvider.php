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

        Route::bind('customer', function ($value) {
            try {
                $id_customer = decrypt($value);
                return \App\Customers::findOrFail($id_customer);
            } catch (DecryptException $e) {
                return abort(404);
            }
        });

        Route::bind('contract', function ($value) {
            try {
                $id_contract = decrypt($value);
                return \App\Contracts::findOrFail($id_contract);
            } catch (DecryptException $e) {
                return abort(404);
            }
        });

        Route::bind('withdraw', function ($value) {
            try {
                $id_withdraw = decrypt($value);
                return \App\Withdrawals::findOrFail($id_withdraw);
            } catch (DecryptException $e) {
                return abort(404);
            }
        });

        Route::bind('yield', function ($value) {
            try {
                $id_yield = decrypt($value);
                return \App\Yields::findOrFail($id_yield);
            } catch (DecryptException $e) {
                return abort(404);
            }
        });

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
