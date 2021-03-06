<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Users extends Model
{
    use SoftDeletes;
    public $table = 'Users';

    public $timestamps = true;

    public $dates = ['updated_at',
                     'registered_at'];

    public $fillable = ['cpf', 'user_table'];

    const CREATED_AT = 'registered_at';
    const DELETED_AT = 'disabled_at';

    public const CPF_REGEX = '/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/m';

    public const USER_VALIDATOR_RULES = [
        'cpf' => ['required',
                  'regex:' . self::CPF_REGEX,
                  'string',
                  'max:14'],
        'user_table' => ['bail',
                         'required',
                         'string',
                         'alpha']
    ];

    /**
      * Create and Saves a new user instance with request param
      *
      * @param \Illuminate\Http\Request $request
      * @return \App\Users|\Illuminate\Support\Facades\Validator
      */
    public static function createByRequest(Request $request) {
      $user_data['user_table'] = $request->input('user_table');

      $validation_result = self::validator($user_data);

      if($validation_result->fails())
        return $validation_result;

      return Users::create($user_data);
    }

    /**
     * Validates the user data with array param
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validator(array $data) {
      return Validator::make($data, [
          'user_table' => self::USER_VALIDATOR_RULES['user_table']
          ]);
    }

    /**
     * Create and Saves the all relationals data of register that the
     * User table has with other tables
     *
     * @param \Illuminate\Http\Request $requestequest
     * @return bool
     */
    public function saveUsersRelationalsTablesData(Request $request) {
      // creates the relationals data tables
      $result = handler()->handleThis(Addresses::createByRequest($request));

      // verifies if the inputs is valids
      if ($result->ifValidationFailsReturnsThis())
        return $result->getLastestValidator();

      $result = handler()->handleThis(Emails::createByRequest($request));

      // verifies if the inputs is valids
      if ($result->ifValidationFailsReturnsThis())
        return $result->getLastestValidator();

      $result = handler()->handleThis(CellPhones::createByRequest($request));

      // verifies if the inputs is valids
      if ($result->ifValidationFailsReturnsThis())
        return $result->getLastestValidator();

      return true;
    }

    /**
     * Verifies if the user is a Customer
     *
     * @return bool
    */
    public function isCustomer() {
        return $this->user_table == 'Customers';
    }

    /**
     * Verifies if the user is a Consultant
     *
     * @return bool
    */
    public function isConsultant() {
        return $this->user_table == 'Consultants';
    }

    /**
     * Gets the child table from this parent table
     *
     * @return App\Consultants|App\Customers
    */
    public function getChild() {
        if ($this->isCustomer()) {
            return Customers::where('cpf', $this->cpf)->firstOrFail();
        } else if ($this->isConsultant()) {
            return Consultants::where('cpf', $this->cpf)->firstOrFail();
        } else {
            throw new Exception('the user has no child, child value is invalid: '
                                . $this->user_table);
        }
    }

    /**
     * Get the user's Email
     *
     * @return string|null
    */
    public function email() {
        return Emails::where('id_user', $this->id)->first();
    }

    /**
     * Gets the user's Address
     *
     * @return \App\Addresses|null
    */
    public function address() {
        return Addresses::where('id_user', $this->id)->first();
    }

    /**
     * Gets the user's cell phones
     *
     * @return mixed
    */
    public function cellPhones() {
        $cellphones = CellPhones::where('id_user', $this->id);
        return $cellphones;
    }
}
