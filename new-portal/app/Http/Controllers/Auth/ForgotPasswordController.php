<?php
// Author: Davi Mendes Pimentel
// last modified date: 13/12/2019

namespace App\Http\Controllers\Auth;

use App\Consultants;
use App\Customers;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMailable;
use App\Users;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public const CUSTOMER_FORGOT_PASS_URL_WITHOUT_PREFIX = 'customer';
    public const CUSTOMER_FORGOT_PASS_URL = 'reset/customer';

    public const CONSULTANT_FORGOT_PASS_URL_WITHOUT_PREFIX = 'consultant';
    public const CONSULTANT_FORGOT_PASS_URL = 'reset/consultant';

    public const ADMIN_FORGOT_PASS_URL_WITHOUT_PREFIX = 'admin';
    public const ADMIN_FORGOT_PASS_URL = 'reset/admin';

    /**
     * Shows the send password reset form for Customers
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showCustomerResetForm() {
        return view('auth.customer.forgot_pass');
    }

    /**
     * Send the email to reset password
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    */
    public function sendCustomerResetPassword() {
        $values = request()->only(['cpf']);

        // verifies if the cpf is correctly formatted
        if($response = handler()->handleThis(Users::userDataValidator($values))
            ->ifValidationFailsRedirect('/')) {
                return $response;
        }

        // gets the user by the cpf
        $customer = Customers::getByCpf($values['cpf']);

        // verifies if the user exists
        if($customer === null)
            return back()->with('error', 'the CPF inputted doesn\'t exists');

        $user_email = (string) $customer->email();

        Mail::to($user_email)->send(new PasswordResetMailable());
    }

    /**
     * Shows the send password reset form for Consultants
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showConsultantResetForm() {
        return view('auth.consultant.forgot_pass');
    }

    /**
     * Send the email to reset password
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    */
    public function sendConsultantResetPassword() {
        $values = request()->only(['cpf']);

        // verifies if the cpf is correctly formatted
        if($response = handler()->handleThis(Users::userDataValidator($values))
            ->ifValidationFailsRedirect('/')) {
                return $response;
        }

        // gets the user by the cpf
        $customer = Consultants::getByCpf($values['cpf']);

        // verifies if the user exists
        if($customer === null)
            return back()->with('error', 'the CPF inputted doesn\'t exists');

        $user_email = (string) $customer->email();

        Mail::to($user_email)->send(new PasswordResetMailable());
    }
}
