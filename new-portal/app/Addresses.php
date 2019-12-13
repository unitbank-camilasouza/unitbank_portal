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

    /***/
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

    // define quais timestamps serão usados
    // para esse modelo
    public $dates = ['updated_at', 'deleted_at'];

    // define o nome de "deletado em"
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
       'street',
       'number',
       'complement',
       'region',
       'city',
       'state',
       'country'
     ]);
     $address_data['id_user'] = session()->get('user_id');  // gets and forget the user id

     self::addressDataValidator($address_data); // verify if the data is valid

     return Addresses::create($address_data); // creates and saves the address
    }

     /**
      * Validates the address data
      *
      * @param array $data
      * @return \Illuminate\Support\Facades\Validator
      */
    public static function addressDataValidator(array $data) {
      return Validator::make($data, [
        'id_user' => ['required', 'integer', 'exists:Users'],
        'street' => ['required', 'string', 'max:150', 'min:3'],
        'number' => ['required', 'integer', 'max:7'],
        'complement' => ['required', 'string', 'max:70'],
        'region' => ['required', 'string', 'max:70'],
        'city' => ['required', 'string', 'max:40'],
        'state' => ['required', 'string', 'max:40'],
        'country' => ['required', 'string', 'max:40'],
      ]);
    }
}
