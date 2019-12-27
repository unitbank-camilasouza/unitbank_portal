<?php

namespace App\Http\Controllers\Auth;

use App\Consultants;
use App\Customers;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
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

    public function __construct() {
        $this->middleware('guest');
    }

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
        return view('auth.admin.login');
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

        if(auth('admin')->attempt($values)) {
            return redirect()->route('home');
        }

        return back()->withInput($values)->with('error', 'invalid credentials');
    }

    /**
     * Login the consultant
     *
     * @return \Illuminate\Http\RedirectResponse
    */
    public function loginConsultant() {
        $values = request()->only(['cpf', 'password']);

        // generates the validator
        $validation_result = Consultants::consultantLoginDataValidator($values);

        // verifies if an invalid input has getted
        if($response = handler()->handleThis($validation_result)->ifValidationFailsRedirect(self::CONSULTANT_LOGIN_URL)) {
            return $response->withErrors($validation_result);
        }

        // tries to login with a consultant account
        if(auth('consultant')->attempt($values))
            return redirect('/home');

        // if the user cannot login, return back with 'invalid credentials' message
        $back_response = back()->with('error_message', 'invalid credentials');
        return $back_response->withInput();
    }

    /**
     * Login the customer
     *
     * @return \Illuminate\Http\RedirectResponse
    */
    public function loginCustomer() {
        $values = request()->only(['cpf', 'password']);

        // generates the validator
        $validation_result = Customers::customerLoginDataValidator($values);

        // verifies if an invalid input has getted
        if($response = handler()->handleThis($validation_result)->ifValidationFailsRedirect(self::CUSTOMER_LOGIN_URL))
            return $response->withErrors($validation_result);

        // tries to login with a consultant account
        if(auth('customer')->attempt($values))
            return redirect()->route('home');

        // if the user cannot login, return back with 'invalid credentials' message
        $back_response = back()->with('error_message', 'invalid credentials');
        return $back_response->withInput();
    }
}
