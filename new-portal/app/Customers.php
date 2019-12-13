<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Http\Request;
use App\Users;

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
      $new_wallet = Wallets::create($request->only('cpf')); // create a new wallet instance

      // gets some data to save customer instance
      $customer_data = $request->only([
        'cpf', 'first_name', 'last_name'
      ]);

      $customer_data['id'] = session()->get('user_id');

      // verify the data passed by request
      self::customerDataValidator($request->all());
      $customer_data['password'] = Users::findOrFail($customer_data['id'])->password;

      $new_customer = self::create($customer_data); // saves the new customer instance

      // creates a new co-wallets instance to
      // make a union with customers and wallets
      $coWallet = CoWalletsJunctions::create([
        'id_customer' => $new_customer->id,
        'id_wallet' => $new_wallet->id
      ]);

      // finally, returns the new customer instance
      return $new_customer;
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

}
