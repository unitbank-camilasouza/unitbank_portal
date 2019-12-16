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

Route::get('/', function () {
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
        Route::get(LoginController::ADMIN_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showAdminLoginForm')
               ->name('admin_login_form');

        Route::post(LoginController::ADMIN_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginAdmin')
               ->name('admin_login');
    });

    // routes to customer login
    Route::get(LoginController::CUSTOMER_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showCustomerLoginForm')
               ->name('customer_login_form');

    Route::post(LoginController::CUSTOMER_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginCustomer')
               ->name('customer_login');

    // routes to consultant login
    Route::get(LoginController::CONSULTANT_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@showConsultantLoginForm')
               ->name('consultant_login_form');

    Route::post(LoginController::CONSULTANT_LOGIN_URL_WITHOUT_PREFIX,
               'Auth\LoginController@loginConsultant')
               ->name('consultant_login');
});

Route::prefix('reset')->middleware(['guest'])->group(function () {

    // routes to forgot admin passwords
    Route::middleware(['ip_access.admin'])->group(function () {
        Route::get(ForgotPasswordController::ADMIN_FORGOT_PASS_URL_WITHOUT_PREFIX,
                   'Auth\ForgotPasswordController@showAdminResetForm')
                   ->name('admin_forgot_password_form');

        Route::post(ForgotPasswordController::ADMIN_FORGOT_PASS_URL_WITHOUT_PREFIX,
                'Auth\ForgotPasswordController@sendAdminResetPassword')
                ->name('admin_send_password_reset');
    });

    // routes to forgot customer passwords
    Route::get(ForgotPasswordController::CUSTOMER_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@showCustomerResetForm')
               ->name('customer_forgot_password_form');

    Route::post(ForgotPasswordController::CUSTOMER_FORGOT_PASS_URL_WITHOUT_PREFIX,
               'Auth\ForgotPasswordController@sendCustomerResetPassword')
               ->name('customer_send_password_reset');

    // routes to forgot consultant passwords
    Route::get(ForgotPasswordController::CONSULTANT_FORGOT_PASS_URL_WITHOUT_PREFIX,
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
        Route::get(RegisterController::CUSTOMER_REGISTER_URL_WITHOUT_PREFIX,
                   'Auth\RegisterController@showCustomerRegisterForm')
               ->name('form_customer_register');

        // route to register a new customer in database
        Route::post(RegisterController::CUSTOMER_REGISTER_URL_WITHOUT_PREFIX,
                   'Auth\RegisterController@registerCustomer')
               ->name('register_customer');
    });

    // routes to register a consultant
    Route::middleware(['auth:admin'])->group(function () {
        Route::get(RegisterController::CONSULTANT_REGISTER_URL_WITHOUT_PREFIX,
                'Auth\RegisterController@showConsultantRegisterForm')
            ->name('form_consultant_register');

        Route::post(RegisterController::CONSULTANT_REGISTER_URL_WITHOUT_PREFIX,
                    'Auth\RegisterController@registerConsultant')
                ->name('register_consultant');
    });
});

Route::prefix('home')->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
});
