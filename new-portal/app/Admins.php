<?php
// Author: Davi Mendes Pimentel
// last modified date: 12/12/2019

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request;

/**
 * Admin Model
*/
class Admins extends Authenticatable
{
    /**
     * Property responsible by show the admin table name
     *
     * @var string $table
    */
    public $table = 'Admins';

    /**
     * Property responsible by show the fillables properties
     *
     * @var array $fillable
    */
    public $fillable = ['login', 'password', 'ip_address'];

    /**
     * Property responsible by show the hidden properties
     *
     * @var array $hidden
    */
    public $hidden = ['password'];

    /**
     * Validates the values passed by array
     *
     * @param array $values
     * @return \Illuminate\Contracts\Validation\Validator
    */
    public function adminDataValidator(array $values) {
        return Validator::make($values, [
            'login' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Verifies if the ip exists on database
     *
     * @param string $ip_address
     * @return App\Admin|null
    */
    public static function ipExists(string $ip_address) {
        return Admins::where('ip_address', $ip_address)->first();
    }
}
