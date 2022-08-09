<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;

use App\IabCountry;
use App\IabRegion;
use App\Languages;
use App\IabCity;
use App\ConversionPixel;

class PixelConversionController extends Controller
{
    public function index() {
        $pixel = ConversionPixel::find(intval($_GET['pixelid']));
        return Voyager::view('voyager::pixelconversion', compact('pixel'));
    }
}
