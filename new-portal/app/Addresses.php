<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request;

class Addresses extends Model
{
    use SoftDeletes;
    public $table = 'Addresses';

    public $timestamps = false;

    /**
     * The fillable fields of the model
     *
     * @var array $fillable
    */
    public $fillable = [
      'id_user',
      'street',
      'number',
      'complement',
      'region',
      'city',
      'state',
      'country'
    ];

    /**
     * The timestamps accepted by de model
     *
     * @var array $dates
    */
    public $dates = ['updated_at', 'deleted_at'];

    /**
     * The 'deleted_at' name on the database
     *
     * @const string DELETED_AT
    */
    const DELETED_AT = 'disabled_at';

    /**
     * Creates and Saves a new address instance in database with request
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Addresses
     */
    public static function createByRequest(Request $request) {
     // gets all data relationated with address
     $address_data = $request->only([
       'id_user',
       'street',
       'number',
       'complement',
       'region',
       'city',
       'state',
       'country'
     ]);

     $validation_result = self::validator($address_data); // verify if the data is valid

     if ($validation_result->fails())
        return $validation_result;

     return Addresses::create($address_data); // creates and saves the address
    }

     /**
      * Validates the address data
      *
      * @param array $data
      * @return \\Illuminate\Contracts\Validation\Validator
      */
    public static function validator(array $data) {
      return Validator::make($data, [
        'id_user' => ['required', 'integer', 'exists:Users,id'],
        'street' => ['required', 'string', 'max:150', 'min:3'],
        'number' => ['required', 'integer', 'max:100000'],
        'complement' => ['required', 'string', 'max:70'],
        'region' => ['required', 'string', 'max:70'],
        'city' => ['required', 'string', 'max:40'],
        'state' => ['required', 'string', 'max:40'],
        'country' => ['required', 'string', 'max:40'],
      ]);
    }
}
