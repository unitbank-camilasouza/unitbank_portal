<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CancellationsSolicitations extends Model
{
    use SoftDeletes;
    public $table = 'CancellationsSolicitations';

    public $fillable = [
        'id_contract', 'id_customer'
    ];

    public $timestamps = false;

    public $dates = ['created_at', 'updated_at', 'deleted_at'];

    const DELETED_AT = 'decided_at';

    /**
     * Creates a new Withdrawal Solicitation by Request
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\WithdrawalsSolicitations|\Illuminate\Contracts\Validation\Validator
     */
    public static function createByRequest(Request $request) {
        $withdraw_solicitation_data = $request->only([
            'id_contract', 'id_customer'
        ]);

        $validation_result = self::validator($withdraw_solicitation_data);
        if($validation_result->fails()) {
            return $validation_result;
        }

        return self::create($withdraw_solicitation_data);
    }

    /**
     * Validate the array data to create a new instance of self
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validator(array $data) {
        return Validator::make($data, [
            'id_contract' => ['required', 'integer', 'exists:Contracts,id'],
            'id_customer' => ['required', 'integer', 'exists:Customers,id'],
        ]);
    }
}
