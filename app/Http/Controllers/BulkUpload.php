<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use TCG\Voyager\Facades\Voyager;
use Carbon\Carbon;

class BulkUpload extends Controller
{
    public function index(){

        $reports = "hola";

        return Voyager::view('voyager::bulkupload', compact('reports'));
    }
}
