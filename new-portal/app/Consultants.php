<?php
// Author: Davi Mendes Pimentel
// last modified date: 11/12/2019

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request;

class Consultants extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'cpf',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * defines if the use will use timestamps fields
     *
     * @var array
     */
    public $timestamps = false;

    /**
     * defines the dates of the user will use
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at'];

    // defines the new name of 'deleted_at' on database
    const DELETED_AT = 'disabled_at';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $table = 'Consultants';

    /**
      * Create and Saves a new consultant instance by request param
      *
      * @param \Illuminate\Http\Request $request
      * @return \App\Consultants
      */
      public static function createByRequest(Request $request) {
        // gets some data to save consultant instance
        $consultant_data = $request->only([
          'cpf', 'first_name', 'last_name', 'password'
        ]);

        $consultant_data['id'] = $request->input('id_user');

        // verify the consultant data
        $validation_result = self::validator($consultant_data);
        if($validation_result->fails())
            return $validation_result;

        $new_consultant = self::create($consultant_data); // saves the new consultant instance

        // finally, returns the new consultant instance
        return $new_consultant;
      }

      /**
       * Validates data of a consultant by the array
       *
       * @param array $data
       * @return \Illuminate\Contracts\Validation\Validator
       */
      public static function validator(array $data) {
        return Validator::make($data, [
          'id' => ['required', 'integer', 'exists:Users'],
          'cpf' => ['required',
                    'regex:' . Users::CPF_REGEX,
                    'string',
                    'max:14',
                    'unique:Consultants'],
          'password' => ['required', 'string', 'max:255'],
          'first_name' => ['required', 'alpha', 'string', 'min:2'],
          'last_name' => ['required', 'alpha', 'string', 'min:2'],
        ]);
      }

      /**
       * Validates login data of a consultant by the array
       *
       * @param array $data
       * @return Illuminate\Support\Facades\Validator
      */
      public static function consultantLoginDataValidator(array $data) {
        return Validator::make($data, [
            'cpf' => ['required',
                    'regex:' . Users::CPF_REGEX,
                    'string',
                    'max:14',
                    'exists:Consultants'],
            'password' => ['required', 'string', 'max:255'],
        ]);
      }

    /**
     * Gets the user by the given CPF
     *
     * @return \App\Consultants
    */
    public static function getByCpf(string $cpf) {
        return self::where('cpf', $cpf)->first();
    }

    /**
     * Gets the main email of a Consultants
     *
     * @return string|null
    */
    public function email() {
        $user = Users::findOrFail($this->id);   // get the parent user instance

        return $user->email();  // gets the email if it exists
    }

    /**
     * Returns $this as a String
     *
     * @return string
     */
    public function __toString() {
        return json_encode($this);
    }
}
