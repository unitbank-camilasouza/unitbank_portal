<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Http\Request;

class PhysicalPersons extends Model
{
    public $table = 'PhysicalPersons';

    /**
     *
     * Allows that this model be saved in database
     *
     * @var array
     */
    public $fillable = ['id',
                         'nationality',
                         'born_date',
                         'gender',
                         'father_name',
                         'mother_name',
                         'marital_status'];
    /**
      *
      * Allows that this model be saved in database
      *
      * @var bool
      */
    public $timestamps = false;

    /**
     * Create and Saves a new physical person instance with request param
     *
     * @param Request $request
     * @return App\PhysicalPersons
     */
    public static function createByRequest(Request $request) {
      $person_data = $request->only([
        'nationality',
        'born_date',
        'gender',
        'father_name',
        'mother_name',
        'marital_status'
      ]);

      $person_data['id'] = session()->get('user_id');

      self::physicalPersonDataValidator($person_data);  // verify data

      return PhysicalPersons::create($person_data);
    }

    /**
     * Validates data of a physical person by the array
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
     public static function physicalPersonDataValidator(array $data) {
       return Validator::make($data, [
         'id' => ['required', 'integer', 'unique:Customers'],
         'nationality' => ['required', 'string', 'exists:Nationalities'],
         'born_date' => ['required', 'date'],
         'gender' => ['required', 'string', 'exists:Genders'],
         'father_name' => ['nullable', 'string'],
         'mother_name' => ['nullable', 'string'],
         'marital_status' => ['required', 'string', 'exists:MaritalStatus'],
       ]);
     }
}
