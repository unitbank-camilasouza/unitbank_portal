<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallets extends Model
{
    use SoftDeletes;
    public $table = 'Wallets';

    public $fillable = [
      'id_last_withdrawn',
      'id_last_contract',
      'id_last_yield',
      'balance',
    ];

    public $timestamps = false;

    public $dates = ['updated_at', 'deleted_at'];

    const DELETED_AT = 'disabled_at';
}
