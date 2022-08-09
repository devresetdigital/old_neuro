<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RsnProgramGenres extends Model
{
    protected $table = 'rsn_program_genres';
    public $timestamps = false;
    protected $fillable = ['name'];
    
}
