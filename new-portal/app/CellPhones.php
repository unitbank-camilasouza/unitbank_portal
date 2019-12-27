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

    const CELLPHONE_REGEX = '/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}$/m';

    /**
     * Validation rules
     *
     * @const array VALIDATION_RULES
    */
    const VALIDATION_RULES = [
        'id_user' => ['required', 'integer', 'exists:Users,id'],
        'phone_number' => ['bail',
                      'required',
                      'string',
                      'regex:' . self::CELLPHONE_REGEX],
    ];

    /**
     * Creates and saves a new cellphone instance in database
     *
     * @param \Illuminate\Http\Request $request
     * @return App\CellPhones
     */
    public static function createByRequest(Request $request) {
      $phone_data = $request->only([
          'phone_number', 'id_user'
      ]);

      $validation_result = self::validator($phone_data);  // verify if the data is correct
      if($validation_result->fails())
        return $validation_result;

      return CellPhones::create($phone_data);  // creates and saves the phone number
    }


    public static function validator(array $data) {
      return Validator::make($data, self::VALIDATION_RULES);
    }
}
