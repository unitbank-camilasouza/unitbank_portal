<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Withdrawals extends Model
{
    use SoftDeletes;

    /**
     * Withdrawals table name
     *
     * @var string $table
    */
    public $table = 'Withdrawals';

    /**
     * Fillables properties
     *
     * @var array $fillable
     */
    public $fillable = [
        'id_contract', 'id_wallet', 'value',
        'previous_value'
    ];

    /** */
    public $timestamps = false;

    /**
     * Create and Saves a new Withdraw instance with request param
     *
     * @param  Illuminate\Http\Request $request
     * @return App\Withdraw|\App\Illuminate\Support\Facades\Validator
    */
    public static function createByRequest(Request $request) {
        $withdraw_data = $request->only([
            'id_contract', 'id_wallet', 'value'
        ]);

        // verifies if the validation fails
        $validation_result = self::validator($withdraw_data);
        if($validation_result->fails())
            return $validation_result;

        // get the current contract of the withdraw and sub the value of it
        $curr_contract = CurrentContracts::findOrFail($withdraw_data['id_contract']);
        $withdraw_data['previous_value'] = $curr_contract->current_value;

        $curr_contract->current_value -= $withdraw_data['value'];

        $curr_contract->save();

        return Withdrawals::create($withdraw_data);
    }

    /**
     * Validates the Withdraw data with array param
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validator($data) {
        return Validator::make($data, [
            'id_contract' => ['bail', 'integer', 'exists:CurrentContracts,id'],
            'id_wallet' => ['bail', 'integer', 'exists:Wallets,id'],
            'value' => ['bail', 'numeric', 'gt:0'],
        ]);
    }

    /**
     * Gets the Withdraw's Contract instance
     *
     * @return \App\Contracts
     */
    public function contract() {
        return Contracts::findOrFail($this->id_contract);
    }
}
