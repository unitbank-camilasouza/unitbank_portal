<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request;

class CellPhones extends Model
{
    use SoftDeletes;
    public $table = 'CellPhones';

    public $fillable = ['id_user', 'phone_number'];

    public $timestamps = false;

    public $dates = ['updated_at', 'disabled_at'];

    const DELETED_AT = 'disabled_at';

    /**
     * Creates and saves a new cellphone instance in database
     *
     * @var \Illuminate\Http\Request $request
     * @return App\CellPhones
     */
    public static function createByRequest(Request $request) {
      if(! session()->has('user_id'))
        throw new Exception('Illegal arguments, \'user_id\' field is missing in the session.');

      $phone_data = $request->only(['phone_number']); // gets the phone number
      $phone_data['id_user'] = session()->get('user_id'); // gets the user id

      self::cellphoneDataValidator($phone_data);  // verify if the data is correct

      return CellPhones::create($phone_data);  // creates and saves the phone number
    }

    public static function cellphoneDataValidator(array $data) {
      return Validator::make($data, [
        'id_user' => ['required', 'integer', 'exists:Users'],
        'phone_number' => ['bail',
                      'required',
                      'string',
                      'regex:^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}$/m'],
      ]);
    }
}
