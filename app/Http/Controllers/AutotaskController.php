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

class AutotaskController extends Controller
{
    public function index()
    {
        //get reports

        $reports=json_decode(file_get_contents("http://104.131.2.141:9000/taskslist"),true);

        //print_r($reports);
        //die();


        return Voyager::view('voyager::autotask', compact('reports'));
    }
}