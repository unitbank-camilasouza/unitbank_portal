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
     * Create and Saves a new Withdraw instance with request param
     *
     * @param  Illuminate\Http\Request $request
     * @return App\Withdraw|\App\Illuminate\Support\Facades\Validator
    */
    public function createByRequest(Request $request) {
        $withdraw_values = $request->only([
            'id_contract', 'id_wallet', 'value'
        ]);

        // verifies if the validation fails
        $validation_result = self::validator($withdraw_values);
        if($validation_result->fails())
            return $validation_result;

        // get the current contract of the withdraw and sub the value of it
        $contract = CurrentContracts::findOrFail($withdraw_values['id_contract']);
        $withdraw_values['previous_value'] = $contract->value;

        $contract->value -= $withdraw_values['value'];
        $contract->save();

        return Withdrawals::create($withdraw_values);
    }

    /**
     * Validates the Withdraw data with array param
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator($data) {
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
