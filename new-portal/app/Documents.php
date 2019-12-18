<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Documents extends Model
{
    protected $table = 'Documents';

    /**
     * Creates a Document by request param
     *
     * @param Illuminate\Http\Request $request
     * @return App\Documents|null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public static function createByRequest(Request $request) {
        $document_data = $request->only([
            'id_physical_person',
            'document_type',
            'doc_number',
            'issue_date',
            'issue_organ',
            'uf',
        ]);

        $validation_result = self::validator($document_data);
        if($response = handler()->handleThis($validation_result)->ifValidationFailsRedirect($request->url()))
            return $response->withErrors($validation_result);

        return self::create($document_data);
    }

    /**
     * Validates Document data with array param
     *
     * @param array $values
     * @return
     */
    public static function validator(array $values) {
        return Validator::make($values, [
            'id_physical_person' => ['required', 'integer', 'unique:PhysicalPersons,id'],
            'document_type' => ['required', 'string', 'unique:DocumentTypes,document_type'],
            'doc_number' => ['required', 'string', 'max:30'],
            'issue_date' => ['required', 'date'],
            'issue_organ' => ['required', 'string', 'max:90'],
            'uf' => ['required', 'string', 'max:2'],
        ]);
    }
}
