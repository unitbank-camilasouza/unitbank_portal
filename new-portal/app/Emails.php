<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Emails extends Model
{
    use SoftDeletes;
    public $table = 'Emails';

    public $fillable = ['email', 'id_user'];

    public $timestamps = false;

    public $dates = ['updated_at', 'disabled_at'];

    const DELETED_AT = 'disabled_at';

    /**
     * Creates and save a new email instance in database by request
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Emails
     * */
     public static function createByRequest(Request $request) {
       $email_data = $request->only(['email']);  // gets the email
       $email_data['id_user'] = session()->get('user_id'); // gets de user id

       self::emailDataValidator($email_data); // verify if the data are correct

       return Emails::create($email_data);  // creates and saves the email
     }

     /**
      * Validates email data by array
      *
      * @param array $data
      * @return Illuminate\Support\Facades\Validator
      */
     public static function emailDataValidator(array $data) {
       return Validator::make($data, [
         'email' => ['required', 'email'],
         'id_user' => ['required', 'integer', 'exists:Users'],
       ]);
     }
}
