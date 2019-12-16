<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Contracts extends Model
{
    use SoftDeletes;
    /**
     * Contracts table name
     *
     * @var string $table
    */
    public $table = 'Contracts';

    const DELETED_AT = 'disabled_at';

    /**
     * Create and Saves a new Contract instance with request param
     *
     * @param  Illuminate\Http\Request $request
     * @return App\Contracts|\App\Illuminate\Support\Facades\Validator
    */
    public static function createByRequest(Request $request) {
        $contract_data = $request->only([
            'id_wallet', 'contract_status', 'product', 'value'
        ]);

        $validation_result = self::validator($contract_data);
        if($validation_result->fails())
            return $validation_result;

        return self::create($contract_data);
    }

    /**
     * Validates the Contract data with array param
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
    public static function validator($data) {
        return Validator::make($data, [
            'id_wallet' => ['bail', 'required', 'integer', 'unique:Wallets,id'],
            'contract_status' => ['bail', 'required', 'string', 'unique:ContractStatus,contract_status'],
            'product' => ['bail', 'required', 'string', 'unique:Products,product'],
            'value' => ['bail', 'required', 'numeric'],
            'started_at' => ['bail', 'date', 'after_or_equal:2019-01-01'],
        ]);
    }
}
