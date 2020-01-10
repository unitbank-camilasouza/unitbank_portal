<?php
// Author: Davi Mendes Pimentel
// last modified date: 12/12/2019

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('/', function () {
    return view('welcome');
})->name('index');

Route::post('logout', function () {
    $current_guard = app()->currentGuard();

    auth($current_guard)->logout();

    return redirect('/');
})->name('logout');

Route::prefix('login')->middleware(['guest'])->group(function () {

    // routes to admin login
    Route::middleware(['ip_access.admin'])->group( function () {
        Route::any(LoginController::ADMIN_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showAdminLoginForm')
               ->name('admin_login_form');

        Route::post(LoginController::ADMIN_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginAdmin')
               ->name('admin_login');
    });

    // routes to customer login
    Route::any(LoginController::CUSTOMER_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showCustomerLoginForm')
               ->name('customer_login_form');

    Route::post(LoginController::CUSTOMER_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginCustomer')
               ->name('customer_login');

    // routes to consultant login
    Route::any(LoginController::CONSULTANT_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showConsultantLoginForm')
               ->name('consultant_login_form');

    Route::post(LoginController::CONSULTANT_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginConsultant')
               ->name('consultant_login');
});

Route::prefix('reset')->middleware(['guest'])->group(function () {

    // routes to forgot admin passwords
    Route::middleware(['ip_access.admin'])->group(function () {
        Route::any(ForgotPasswordController::ADMIN_FORGOT_PASS_URL_WITHOUT_PREFIX,
                   'Auth\ForgotPasswordController@showAdminResetForm')
                   ->name('admin_forgot_password_form');

        Route::post(ForgotPasswordController::ADMIN_FORGOT_PASS_URL_WITHOUT_PREFIX,
                'Auth\ForgotPasswordController@sendAdminResetPassword')
                ->name('admin_send_password_reset');
    });

    // routes to forgot customer passwords
    Route::any(ForgotPasswordController::CUSTOMER_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@showCustomerResetForm')
               ->name('customer_forgot_password_form');

    Route::post(ForgotPasswordController::CUSTOMER_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@sendCustomerResetPassword')
               ->name('customer_send_password_reset');

    // routes to forgot consultant passwords
    Route::any(ForgotPasswordController::CONSULTANT_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@showConsultantResetForm')
               ->name('consultant_forgot_password_form');

    // route to send the reset password link
    Route::post(ForgotPasswordController::CONSULTANT_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@sendConsultantResetPassword')
               ->name('consultant_send_password_reset');
});

Route::prefix('register')->middleware(['auth'])->group(function () {

    // routes to register a customer
    Route::middleware(['auth:consultant'])->group( function () {
        // route to show form to create a new customer
        Route::any(RegisterController::CUSTOMER_REGISTER_URL_WITHOUT_PREFIX,
                   'Auth\RegisterController@showCustomerRegisterForm')
               ->name('form_customer_register');

        // route to register a new customer in database
        Route::post(RegisterController::CUSTOMER_REGISTER_URL_WITHOUT_PREFIX,
                   'Auth\RegisterController@registerCustomer')
               ->name('register_customer');
    });

    // routes to register a consultant
    Route::middleware(['auth:admin'])->group(function () {
        Route::any(RegisterController::CONSULTANT_REGISTER_URL_WITHOUT_PREFIX,
                'Auth\RegisterController@showConsultantRegisterForm')
            ->name('form_consultant_register');

        Route::post(RegisterController::CONSULTANT_REGISTER_URL_WITHOUT_PREFIX,
                    'Auth\RegisterController@registerConsultant')
                ->name('register_consultant');
    });
});

Route::prefix('home')->middleware('auth')->group(function () {
    Route::any('/', 'HomeController@index')->name('home');





    /** Customers management routes */
    Route::prefix('users', function () {
        // shows all customers
        Route::any('/', 'CustomerController@showCustomers')
             ->middleware('auth:consultant')
             ->name('show_all_customers');

        // show a profile of a specifc customer
        Route::any('id/{customer}', 'CustomerController@showProfile')
             ->middleware('can:request-user-details,customer')
             ->name('show_customer');
    });






    /** Contracts management routes */
    Route::prefix('contracts')->group(function () {
        // shows all contracts
        Route::any('/', 'ContractController@showsContracts')
             ->name('show_all_contracts');

        Route::prefix('id/{contract}')->group( function () {

            // show contract's details
            Route::any('/', 'ContractController@showsContractDetails')
             ->middleware('can:request-contract-details,contract')
             ->name('show_contract_details');

            // route to disable a contract
            Route::post('disable', 'ContractController@disableContract')
                 ->middleware('auth:consultant')
                 ->name('disable_contract');
        });

        // route to show the save contract form
        Route::get('save-new-contract', 'ContractController@showSaveContractForm')
             ->middleware('auth:consultant')
             ->name('save_new_contract_form');

        // route to save a new contract
        Route::post('save-new-contract', 'ContractController@saveContract')
             ->middleware('auth:consultant')
             ->name('save_new_contract');
    });






    /** Withdrawals management routes */
    Route::prefix('withdrawals')->group(function () {
        // shows all withdrawals
        Route::any('/', 'WithdrawController@showsWithdraws');

        // route to view a specif withdraw
        Route::prefix('id/{withdraw}')->group(function () {

            Route::any('/', 'WithdrawController@showsWithdrawDetails')
               ->middleware(['can:request-contract-details,withdraw'])
               ->name('show_withdraw_details');

            // route to disable a withdrawn
            Route::post('disable', 'WithdrawController@disableWithdrawn')
                 ->middleware('auth:consultant')
                 ->name('disable_withdrawn');
        });

        // route to send/make a withdraw solicitation
        Route::post('send-withdraw-soliciation/{contract}',
                    'SolicitationController@makeWithdrawSolicitation')
             ->middleware(['auth:customer', 'can:request-contract-details,contract'])
             ->name('send_withdraw_solicitation');

        // route to send/make a cancellation solicitation
        Route::post('send-cancellation-solicitation/{contract}',
                    'SolicitationController@makeCancelSolicitation')
             ->middleware(['auth:customer', 'can:request-contract-details,contract'])
             ->name('send_cancellation_solicitation');

        // route to show the withdraw form
        Route::get('make-a-withdraw/{contract?}', 'WithdrawController@showWithdrawForm')
             ->middleware('auth:consultant')
             ->name('make_withdraw_form');

        // route to save a withdraw
        Route::post('make-a-withdraw/{contract}', 'WithdrawController@makeAWithdraw')
             ->middleware('auth:consultant')
             ->name('make_withdraw');
    });






    /** Yields management routes */
    Route::prefix('yields')->group(function () {
        // shows all yields
        Route::any('/', 'YieldController@showsYields')
             ->name('show_all_yields');

        // route to view a specif yield
        Route::any('id/{yield}', 'YieldController@showsYieldDetails')
             ->middleware(['can:request-contract-details,yield'])
             ->name('show_yield_details');

        // route to approves a yield
        Route::post('yield-approvation', 'YieldController@makeAYield')
             ->middleware(['auth:consultant'])
             ->name('approves_yield');

        // route to disable a yield
        Route::post('disable-yield/{yield}', 'YieldController@disableYield')
             ->middleware(['auth:consultant'])
             ->name('disable_yield');
    });
});
