<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
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
     * Create and Saves a new Contract instance with request param
     *
     * @param  \Illuminate\Http\Request $request
     * @return \App\Contracts|\App\Illuminate\Support\Facades\Validator
    */
    public static function createByRequest(Request $request) {
        $contract_data = $request->only([
            'id_wallet', 'contract_status', 'product', 'value'
        ]);

        $validation_result = self::validator($contract_data);
        if($validation_result->fails())
            return $validation_result;

        $new_contract = self::create($contract_data);

        $request->merge([
            'id' => $new_contract->id
        ]);

        $validation_result = CurrentContracts::createByRequest($request);

        if(handler()->handleThis($validation_result)->ifValidationFailsReturnsThis()) {
            return $validation_result;
        }

        return $new_contract;
    }

    /**
     * Validates the Contract data with array param
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
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

    /**
     * Gets this Contract's Customers
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function customers() {
        $co_wallets = $this->coWallets();
        $co_wallets->join;

        // return $this->belongsToMany('\App\Customers');
    }

    /**
     * Gets this Contract's CoWallets instances
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function coWallets() {
        return CoWalletsJunctions::where('id_wallet', $this->id_wallet);
    }

    /**
     * Gets this Contract's CurrentContract instance
     *
     * @return \App\CurrentContracts
     */
    public function currentContract() {
        return CurrentContracts::findOrFail($this->id);
    }

    /**
     * Gets this Contract's Withdrawals instances
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function withdrawals() {
        return Withdrawals::where('id_contract', $this->id);
    }

    /**
     * Gets this Contract's Yields instances
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function yields() {
        return Yields::where('id_contract', $this->id);
    }
}
