<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Concept;
use App\Creative;
use App\IabCity;
use App\IabCountry;
use App\IabRegion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;

class NewDashboardController extends Controller
{
    public function index()
    {

        $welcome = "welcome";

        return Voyager::view('voyager::newdashboard', compact('reports','welcome'));
    }
}