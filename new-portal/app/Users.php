<?php

namespace App;

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

    public $fillable = ['cpf', 'password'];

    const CREATED_AT = 'registered_at';
    const DELETED_AT = 'disabled_at';

    /**
      * Create and Saves a new user instance with request param
      *
      * @param Request $request
      * @return App\Users|Illuminate\Support\Facades\Validator
      */
    public static function createByRequest(Request $request) {
      $user_data['password'] = Hash::make(
        $request->get('password')
      );
      $user_data['cpf'] = request()->get('cpf');

      $validator = self::userDataValidator($user_data);

      if($validator->fails())
        return $validator;

      return Users::create($user_data);
    }

    /**
     * Validates the user data with array param
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
    public static function userDataValidator(array $data) {
      return Validator::make($data, [
        'cpf' => ['required',
                  'regex:/^[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$/m',
                  'string',
                  'max:14'],
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
      // verify if the 'user_id' is already on the session
      if(! session()->has('user_id'))
        session()->put('user_id', $this->id); // if not, put it in the session

      // creates the relationals data tables
      $validation_result = handler()->handleThis(Addresses::createByRequest($request));
      $validation_result->ifValidationFailsRedirect('/register');

      $validation_result = handler()->handleThis(Emails::createByRequest($request));
      $validation_result->ifValidationFailsRedirect('/register');

      $validation_result = handler()->handleThis(CellPhones::createByRequest($request));
      $validation_result->ifValidationFailsRedirect('/register');

      session()->forget('user_id'); // flush the 'user_id'

      return true;
    }
}
