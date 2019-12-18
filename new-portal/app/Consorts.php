<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Consorts extends Model
{
    protected $table = 'Consorts';

    /**
     * Validates a array data to a consort
     *
     * @param array $values
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($values) {
        return Validator::make($values, [
            'id' => ['required', 'integer'],
            'id_physical_person' => ['required', 'integer', 'unique:PhysicalPersons,id'],
            'marital_status' => ['required', 'string', 'unique:MaritalStatus,marital_status'],
            'name' => ['required', 'string', 'max:90'],
            'cpf' => ['required', 'string', 'max:14'],
            'gender' => ['required', 'string', 'unique:Genders,gender'],
        ]);
    }

    /**
     * Creates a new Consorts instance by request
     *
     * @param Illuminate\Http\Request $request
     * @return App\Consorts|null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public static function createByRequest(Request $request) {
        $consort_data = [
            'id_physical_person' => $this->id,
            'marital_status' => $this->marital_status,
            'name' => $request->input('consort_name'),
            'cpf' => $request->input('consort_cpf'),
            'gender' => $request->input('consort_gender')
        ];

        $result = self::validator($consort_data);
        if($response = handler()->handleThis($result)->ifValidationFailsRedirect($request->url()))
            return $response->withErrors($result);

        return self::create($consort_data);
    }
}
