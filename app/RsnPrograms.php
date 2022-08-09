<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnPrograms extends Model
{
    protected $table = 'rsn_programs';
    public $timestamps = false;
    protected $fillable = ['name'];
    
}
