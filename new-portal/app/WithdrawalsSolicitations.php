<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawalsSolicitations extends Model
{
    use SoftDeletes;
    public $table = 'WithdrawalsSolicitations';

    public $fillable = [
        'value', 'id_contract', 'id_customer'
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
            'value', 'id_contract', 'id_customer'
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
            'value' => ['required', 'numeric', 'gt:0'],
            'id_contract' => ['required', 'integer', 'exists:Contracts,id'],
            'id_customer' => ['required', 'integer', 'exists:Customers,id'],
        ]);
    }
}
