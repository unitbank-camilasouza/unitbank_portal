<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CurrentContracts extends Model
{
    /**
     * table name
     *
     * @var string $table
    */
    protected $table = 'CurrentContracts';

    /**
     * The name of 'deleted_at' column
     *
     * @const string
     */
    const DELETED_AT = 'disabled_at';

    /**
     * The timestamps boolean property
     *
     * @var boolean $timestamps
     */
    protected $timestamps = false;

    /**
     * The available dates columns
     *
     * @var array $dates
     */
    protected $dates = ['created_at', 'deleted_at'];

    /**
     * Create a new instance by request
     *
     * @param Illuminate\Http\Request $request
     * @return \App\CurrentContracts|\Illuminate\Contracts\Validation\Validator
     */
    public static function createByRequest(Request $request) {
        $current_contract_data = $request->only([
            'id'
        ]);
        $current_contract_data['current_value'] = $request->input('value');

        $validation_result = self::validator($current_contract_data);
        if($validation_result->fails())
            return $validation_result;

        return self::create();
    }

    /**
     * Validates the data by the array param
     *
     * @param array $values
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validator(array $values) {
        return Validator::make($values, [
            'id' => ['required', 'integer', 'unique:Contracts,id'],
            'current_value' => ['required', 'numeric', 'gt:0'],
        ]);
    }
}
