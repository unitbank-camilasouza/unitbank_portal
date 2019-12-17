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

Route::prefix('home')->middleware(['auth'])->group(function () {
    Route::any('/', 'HomeController@index')->name('home');





    /** Customers management routes */
    Route::prefix('users', function () {
        // shows all customers
        Route::any('/', 'CustomerController@showCustomers');

        // show a profile of a specifc customer
        Route::any('{customer}', 'CustomerController@showProfile');
    });






    /** Contracts management routes */
    Route::prefix('contracts')->group(function () {
        // shows all contracts
        Route::any('/', 'ContractController@showsContracts');

        Route::prefix('{contract}')->middleware(['can:request-contract-details,contract'])
             ->group(function () {
            // route to view a specif contract
            Route::any('/', 'ContractController@showsContractDetails');

            // route to show the withdraw form
            Route::get('make-a-withdraw', 'WithdrawController@showWithdrawForm');

            // route to save a withdraw
            Route::post('make-a-withdraw', 'WithdrawController@makeAWithdraw');

            // route to approves a yield
            Route::post('yield-approvation', 'YieldController@makeAYield');
        });

        // route to show the save contract form
        Route::get('save-new-contract', 'ContractController@showSaveContractForm')
               ->middleware(['auth:consultant']);

        // route to save a new contract
        Route::post('save-new-contract', 'ContractController@saveContract')
               ->middleware(['auth:consultant']);
    });






    /** Withdrawals management routes */
    Route::prefix('withdrawals')->group(function () {
        // shows all withdrawals
        Route::any('/', 'WithdrawController@showsWithdraws');

        // route to view a specif withdraw
        Route::any('{withdraw}', 'WithdrawController@showsWithdrawDetails')
               ->middleware(['can:request-withdraw-details,withdraw']);

        // route to show the withdraw form
        Route::get('make-a-withdraw', 'WithdrawController@showWithdrawForm');

        // route to save a withdraw
        Route::post('make-a-withdraw', 'WithdrawController@makeAWithdraw');
    });






    /** Yields management routes */
    Route::prefix('yields')->group(function () {
        // shows all yields
        Route::any('/', 'YieldController@showsYields');

        // route to view a specif yield
        Route::any('{yield}', 'YieldController@showsYieldDetails')
               ->middleware(['can:request-yield-details,yield']);

        // route to approves a yield
        Route::post('yield-approvation', 'YieldController@makeAYield');
    });
});
