<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genders extends Model
{
    public $table = 'Genders';

    public $fillable = ['gender'];
}
