<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnDayparts extends Model
{
    protected $table = 'rsn_dayparts';
    public $timestamps = false;
    protected $fillable = ['name','description'];
}
