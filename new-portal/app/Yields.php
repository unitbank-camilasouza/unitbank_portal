<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class Yields extends Model
{
    use SoftDeletes;
    /**
     * Yield's table name
     *
     * @var string $table
    */
    public $table = 'Yields';

    /**
     * Yield's fillable properties
     *
     * @var array $fillable
     */
    public $fillable = [
        'id_contract', 'value', 'yielded_at',
    ];

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
    public $timestamps = false;

    /**
     * The available dates columns
     *
     * @var array $dates
     */
    protected $dates = ['created_at', 'deleted_at'];

    /**
     * Create and Saves a new Yield instance with request param
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Yields|\App\Illuminate\Support\Facades\Validator
    */
    public static function createByRequest(Request $request) {
        $yield_data = $request->only([
            'id_contract', 'value'
        ]);

        $validation_result = self::validator($yield_data);
        if($validation_result->fails())
            return $validation_result->errors();

        return self::create($yield_data);
    }

    /**
     * Validates the Yield data with array param
     *
     * @param array $data
     * @return \Illuminate\Yields\Validation\Validator
     */
    public static function validator($data) {
        return Validator::make($data, [
            'id_contract' => ['bail', 'required', 'integer', 'exists:Contracts,id'],
            'value' => ['bail', 'required', 'numeric', 'gt:0'],
        ]);
    }
}
