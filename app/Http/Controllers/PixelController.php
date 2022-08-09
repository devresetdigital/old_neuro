<?php

namespace App\Http\Controllers;

use App\Pixel;

class PixelsController extends Controller
{

    public function index(){
        return Pixel::get();
    }
  
}
