<?php

namespace App\Http\Controllers\Auth;

use App\Consultants;
use App\Customers;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public const ADMIN_LOGIN_URL_WITHOUT_PREFIX = 'admin';
    public const ADMIN_LOGIN_URL = '/login/admin';

    public const CUSTOMER_LOGIN_URL_WITHOUT_PREFIX = 'customer';
    public const CUSTOMER_LOGIN_URL = '/login/customer';

    public const CONSULTANT_LOGIN_URL_WITHOUT_PREFIX = 'consultant';
    public const CONSULTANT_LOGIN_URL = '/login/consultant';

    /**
     * Shows the admin login form
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showAdminLoginForm() {
        return view('admin.login');
    }

    /**
     * Shows the consultant login form
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showConsultantLoginForm() {
        return view('auth.consultant.login');
    }

    /**
     * Shows the customer login form
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showCustomerLoginForm() {
        return view('auth.customer.login');
    }

    /**
     * Login the admin
     *
     * @return \Illuminate\Http\RedirectResponse
    */
    public function loginAdmin() {
        $values = request()->only(['login', 'password']);

        if(auth('auth:admin')->attempt($values))
            return redirect('/home');

        return back()->withInput($values);
    }

    /**
     * Login the consultant
     *
     * @return \Illuminate\Http\RedirectResponse
    */
    public function loginConsultant() {
        $values = request()->only(['cpf', 'password']);

        $validator_result = Consultants::customerLoginDataValidator($values);

        if($response = handler()->handleThis($validator_result)->ifValidationFailsRedirect('/'))
            return $response;

        if(auth('auth:consultant')->attempt($values))
            return redirect('/home');

        $back_response = back()->with('error', 'cpf/password incorrect');
        return $back_response->withInput();
    }

    /**
     * Login the customer
     *
     * @return \Illuminate\Http\RedirectResponse
    */
    public function loginCustomer() {
        $values = request()->only(['cpf', 'password']);

        $validator_result = Customers::customerLoginDataValidator($values);

        if($response = handler()->handleThis($validator_result)->ifValidationFailsRedirect('/'))
            return $response;

        if(auth('auth:customer')->attempt($values))
            return redirect('/home');

        $back_response = back()->with('error', 'cpf/password incorrect');
        return $back_response->withInput();
    }
}
