<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nationalities extends Model
{
    /**
     * The attributes that says the table name to the model operations
     *
     * @var string
     */
    public $table = 'Nationalities';

    public $fillable = ['nationality'];
}
