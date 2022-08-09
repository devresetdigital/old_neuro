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

class ForecastingController extends Controller
{
    public function index()
    {
        $reports="";

        $from = Carbon::now()->subDays(6)->format("ymdH");
        $until = Carbon::now()->format("ymdH");

        //Campaigns
        $campaigns = Campaign::all();
        //Concepts
        $concepts = Concept::all();

        //Creatives
        $creatives = Creative::all();

        //Domains

        //Countries
        $countries = IabCountry::all();

        //Regions
        $regions = IabRegion::all();

        return Voyager::view('voyager::forecasting', compact('reports','from','until','campaigns','concepts','creatives','countries','regions'));
    }
}