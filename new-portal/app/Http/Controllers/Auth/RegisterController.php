<?php
// Author: Davi Mendes Pimentel
// last modified date: 11/12/2019

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Customers;
use App\Users;
use App\Consultants;
use App\PhysicalPersons;
use App\LegalPersons;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;
use \Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $requestedirectTo = null;

    public const CONSULTANT_REGISTER_URL_WITHOUT_PREFIX = 'consultant';
    public const CONSULTANT_REGISTER_URL = '/register/consultant';

    public const CUSTOMER_REGISTER_URL_WITHOUT_PREFIX = 'customer';
    public const CUSTOMER_REGISTER_URL = '/register/customer';

    /**
     * Register a new Customer
     *
     * @return mixed
    */
    public function registerCustomer(Request $request) {
      $this->middleware('auth:consultant'); // verify if the current user is a consultant

      // creates a new transaction to errors cases
      return DB::transaction( function () use ($request) {
        $request->merge([
            'user_table' => 'Customers',
        ]);

        $result = Users::createByRequest($request);  // save a new user

        // verify if any invalid input has ocurred
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL))
            return $response->withErrors($result);

        $new_user = $result;

        $request->merge(['id_user' => $new_user->id]);

        $result = Customers::createByRequest($request);  // save a new customer

        // verify if any invalid input has ocurred
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL)) {
            DB::rollBack();
            return $response->withErrors($result);
        }

        // verify which person type is the user
        if($request->input('person_type', 'physical_person') == 'physical_person') {
            $result = PhysicalPersons::createByRequest($request);  // if is a physical person

            if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL)){
                DB::rollBack();
                return $response->withErrors($result);
            }
        }
        else if($request->input('person_type') == 'legal_person') {
            $result = LegalPersons::createByRequest($request); // if is a legal person

            if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL)){
                DB::rollBack();
                return $response->withErrors($result);
            }
        }
        else
          return back(); // if is undefined, may be a hacker

        // verify if any invalid input has ocurred
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL)){
            DB::rollBack();
            return $response->withErrors($result);
        }

        $result = $new_user->saveUsersRelationalsTablesData($request);
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CUSTOMER_REGISTER_URL)){
            DB::rollBack();
            return $response->withErrors($result);
        }
        DB::commit();

        return back();
      });
    }

    /**
     * Register a new Consultant
     *
     * @return mixed
    */
    public function registerConsultant(Request $request) {
        $this->middleware('auth:admin');    // verify if the current user is an admin

        // creates a new transaction to errors cases
        return DB::transaction( function () use ($request) {
            $request->merge([
                'user_table' => 'Consultants',
            ]);

            $result = Users::createByRequest($request);  // save a new user

            // verify if any invalid input has occurred
            if($error_response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CONSULTANT_REGISTER_URL)) {
                DB::rollBack();
                return $error_response->withErrors($result);
            }

            $new_user = $result;

            // merge the new user ID with the request
            $request->merge([
                'id_user' => $new_user->id
            ]);

            $result = Consultants::createByRequest($request);  // save a new customer

            // verify if any invalid input has occurred
            if($error_response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CONSULTANT_REGISTER_URL)){
                DB::rollBack();
                return $error_response->withErrors($result);
            }

            $result = $new_user->saveUsersRelationalsTablesData($request);

            // verify if any invalid input has occurred
            if($error_response = handler()->handleThis($result)->ifValidationFailsRedirect(self::CONSULTANT_REGISTER_URL)){
                DB::rollBack();
                return $error_response->withErrors($result);
            }

            DB::commit();
            return back();
        });
    }

    /**
     * Shows the form to register a new Consultant
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showConsultantRegisterForm() {
        return view('auth.consultant.register');
    }

    /**
     * Shows the form to register a new Customer
     *
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
    */
    public function showCustomerRegisterForm() {
        return view('auth.customer.register');
    }
}
