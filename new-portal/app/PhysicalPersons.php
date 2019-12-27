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

        $person_data['id'] = $request->input('id_user');

        // verify data
        $validation_result = self::physicalPersonDataValidator($person_data);
        if($validation_result->fails())
            return $validation_result;

        $new_physical_person = PhysicalPersons::create($person_data);

        // verifies if the new user is married
        if($request->input('marital_status') == 'married') {
            $new_physical_person->saveConsort($request);
        }

        $request->merge([
            'id_physical_person' => $new_physical_person->id,
        ]);

        $new_document_result = $new_physical_person->saveDocuments($request);
        if(handler($new_document_result)->ifValidationFailsReturnsThis())
            return $new_document_result;

        return $new_physical_person;
    }

    /**
     * Validates data of a physical person by the array
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function physicalPersonDataValidator(array $data) {
      return Validator::make($data, [
        'id' => ['required', 'integer', 'exists:Customers'],
        'nationality' => ['required', 'string', 'exists:Nationalities'],
        'born_date' => ['required', 'date'],
        'gender' => ['required', 'string', 'exists:Genders'],
        'father_name' => ['nullable', 'string'],
        'mother_name' => ['nullable', 'string'],
        'marital_status' => ['required', 'string', 'exists:MaritalStatus'],
      ]);
    }

    /**
     * Saves a Consort on database by request
     *
     * @param Illuminate\Http\Request $request
     * @return App\Consorts|null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function saveConsort(Request $request) {
        $request->merge([
            'id_physical_person' => $this->id,
            'marital_status' => $this->marital_status,
        ]);
        return Consorts::createByRequest($request);
    }

    /**
     * Saves Documents on the database
     *
     * @param Illuminate\Http\Request $request
     * @return App\Documents
     */
    public function saveDocuments(Request $request) {
        $request->merge([
            'id_physical_person' => $this->id
        ]);
        return Documents::createByRequest($request);
    }
}
