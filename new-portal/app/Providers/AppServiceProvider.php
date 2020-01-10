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

        Gate::define('request-contract-details', function ($user, $details_item) {
            if($user instanceof \App\Customers) {

                if ($details_item instanceof \App\Contracts) {
                     $co_wallet = CoWalletsJunctions::where('id_wallet', $details_item->id_wallet)
                                                    ->firstOrFail();
                }
                else if ($details_item instanceof \App\Withdrawals ||
                         $details_item instanceof \App\Yields)
                {
                            $contract = Contracts::findOrFail($details_item->id_contract);
                            $co_wallet = CoWalletsJunctions::
                                         where('id_wallet', $contract->id_wallet)
                                       ->firstOrFail();
                }

                return $user->id == $co_wallet->id_customer;
            }

            return true;
        });

        Gate::define('request-user-details', function ($user, $user_item) {
            // verifies if the user is a customer
            // if yes, returns the id's validation
            if ($user instanceof \App\Customers) {
                return $user->id == $user_item->id;
            }

            // if not, return true (anybody that isn't a customer can see this user)
            return true;
        });
    }
}
