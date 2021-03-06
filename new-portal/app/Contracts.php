<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CurrentContracts;

class Contracts extends Model
{
    use SoftDeletes;
    /**
     * Contract's table name
     *
     * @var string $table
    */
    public $table = 'Contracts';

    /**
     * Contract's fillable properties
     *
     * @var array $fillable
     */
    public $fillable = [
        'id_wallet', 'contract_status', 'product',
        'value', 'started_at'
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
            return $validation_result->errors();

        $new_contract = self::create($contract_data);

        $request->merge([
            'id' => $new_contract->id
        ]);

        $validation_result = CurrentContracts::createByRequest($request);

        if(handler()->handleThis($validation_result)->ifValidationFailsReturnsThis()) {
            return $validation_result->errors();
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
            'id_wallet' => ['bail', 'required', 'integer', 'exists:Wallets,id'],
            'contract_status' => ['bail', 'required', 'string', 'exists:ContractStatus,status'],
            'product' => ['bail', 'required', 'string', 'exists:Products,product'],
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
        $wallet = $this->wallet();
        return $wallet->belongsToMany('\App\Customers',
                                      'CoWalletsJunctions',
                                      'id_wallet',
                                      'id_customer');
    }

    /**
     * Gets the Contract's wallet
     *
     * @return \App\Wallets
     */
    public function wallet() {
        return Wallets::find($this->id_wallet);
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
