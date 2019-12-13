<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoWalletsJunctions extends Model
{
    public $fillable = ['id_customer', 'id_wallet'];

    public $table = 'CoWalletsJunctions';

    public $timestamps = false;
}
