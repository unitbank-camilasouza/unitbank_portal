<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LegalPersons extends Model
{
  use SoftDeletes;
  public $table = 'LegalPersons';

  public $fillable = ['id', 'id_company', 'id_branch', 'office', 'main_money_source'];

  public $timestamps = true;

  /**
   * Creates and Saves a legal persons instance in database by request
   *
   * @param \Illuminate\Http\Request $r
   * @return App\LegalPersons
   */
  public static function createByRequest(Request $request) {
    // creates a new company with the request data
    $company_data = $request->only([
      'cnpj'
    ]);
    $company_data['name'] = $request->only(['company_name']);
    $new_company = Companies::create($company_data);

    // gets the data from request
    $person_data = $request->only([
      'office', 'main_money_source'
    ]);

    $person_data['id'] = session()->get('user_id');
    $person_data['id_company'] = $new_company->id;

    self::legalPersonDataValidator($person_data);

    // finally creates the new physical person
    return LegalPersons::create($person_data);
  }

  /**
   * Validates legal persons data by array
   *
   * @param array $data
   * @return Illuminate\Support\Facades\Validator
   */
  public static function legalPersonDataValidator(array $data) {
    return Validator::make($data, [
      'id' => ['required', 'integer', 'exists:Users'],
      'id_company' => ['required', 'integer', 'exists:Companies'],
      'id_branch' => ['required', 'integer', 'exists:Branches'],
      'office' => ['required', 'string'],
      'cnpj' => ['required',
                 'string',
                 'regex:/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\/[0-9]{4}\-[0-9]{2}$/m'],
    ]);
  }
}
