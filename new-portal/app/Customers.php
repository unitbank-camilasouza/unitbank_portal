<?php

namespace App;

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Http\Request;
use App\Users;
use Illuminate\Support\Facades\DB;

class Customers extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'id_consultant',
        'id_wallet',
        'first_name',
        'last_name',
        'cpf',
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Defines if the customer model will use timestamps columns
     *
     * @var array
     */
    public $timestamps = false;

    /**
     * Defines which timestamps columns the customer model will use
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at'];

    /**
     * Defines the new name of the 'deleted_at' column
     *
     * @var string
     */
    const DELETED_AT = 'disabled_at';

    /**
     * Defines the new name of the 'updated_at' column
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The attributes that says the table name to the model operations
     *
     * @var string
     */
    public $table = 'Customers';

    /**
      * Create and Saves a new customer instance by request param
      *
      * @param Request $request
      * @return App\Customers
      */
    public static function createByRequest(Request $request) {
      // gets some data to save customer instance
      $customer_data = $request->only([
        'cpf', 'first_name', 'last_name'
      ]);

      $customer_data['id'] = $request->input('user_id');
      $customer_data['password'] = Hash::make(
        $request->input('password')
      );
      $customer_data['financial_profile'] = $request->input('financial_profile');

      // verify the data passed by request
      $result = self::customerDataValidator($customer_data);
      if($response = handler()->handleThis($result)->ifValidationFailsRedirect($request->url()))
        return $response->withErrors($result);

      $new_customer = self::create($customer_data); // saves the new customer instance

      // creates a new instance of wallet
      $new_customer->createWallet();

      // finally, returns the new customer instance
      return $new_customer;
    }

    /**
     * Saves a new relationship instance with wallets by request
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     */
    public function createWallet(Request $request) {
        $new_wallet = Wallets::create(); // create a new wallet instance

        // creates a new co-wallets instance to
        // make a union with customers and wallets
        CoWalletsJunctions::create([
            'id_customer' => $this->id,
            'id_wallet' => $new_wallet->id
        ]);

        return true;
    }

    /**
     * Validates data of a customer by the array
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
     */
    public static function customerDataValidator(array $data) {
      return Validator::make($data, [
        'id' => ['required', 'integer', 'unique:Users'],
        'cpf' => ['required',
                  'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}$/m',
                  'string',
                  'max:14',
                  'unique:Customers'],
        'password' => ['required', 'string', 'max:255'],
        'financial_profile' => ['required', 'string', 'exists:FinancialProfiles'],
        'first_name' => ['required', 'alpha', 'string', 'min:2'],
        'last_name' => ['required', 'alpha', 'string', 'min:2'],
      ]);
    }

    /**
     * Validates login data of a customer by the array
     *
     * @param array $data
     * @return Illuminate\Support\Facades\Validator
    */
    public static function customerLoginDataValidator(array $data) {
        return Validator::make($data, [
            'cpf' => ['required',
                      'regex:' . Users::CPF_REGEX,
                      'string',
                      'max:14',
                      'unique:Customers'],
            'password' => ['required', 'string', 'max:255']
        ]);
    }

    /**
     * Gets the user by the given CPF
     *
     * @return null|App\Customer
    */
    public static function getByCpf(string $cpf) {
        return self::where('cpf', $cpf)->first();
    }

    /**
     * Gets the main email of an Customer
     *
     * @return string|null
    */
    public function email() {
        $user = Users::findOrFail($this->id);   // get the parent user instance

        return $user->email();  // gets the email if it exists
    }

    /**
     * Gets all customer's CoWallets
     *
     * @return App\CoWalletsJunctions
     */
    public function coWalletsJunctions() {
        return $this->belongsToMany('\App\CoWalletsJunctions');
    }

    /**
     * Gets all customer's Contracts
     *
     * @return App\Contracts
     */
    public function contracts() {
        $contracts = DB::table('Contracts')
                         ->join('CoWalletsJunctions', function ($join) {
                             $join->on('CoWalletsJunctions.id_wallet', '=', 'Contracts.id_wallet')
                                  ->on('CoWalletsJunctions.id_customer', '=', $this->id);
                         })
                         ->join('Customers',
                                'CoWalletsJunctions.id_customer', '=', $this->id)
                         ->get();

        return $contracts;
    }
}
