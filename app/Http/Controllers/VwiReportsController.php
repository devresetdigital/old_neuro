<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Strategy;
use App\StrategyConcept;
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

class VwiReportsController extends Controller
{
    public function index()
    {
        $campaign_id = $_GET["campaign_id"]-($_ENV["WL_PREFIX"]*1000000);
       // echo $campaign_id;
        //die();
        $reports="";

        $from = Carbon::now()->subDays(6)->format("ymdH");
        $until = Carbon::now()->format("ymdH");

        //Campaigns
        $campaigns = Campaign::all();

        //Strategies
        $strategies = Strategy::with('StrategyConcept')->where('campaign_id','=',$campaign_id)->get();
        $concepts = array();
        foreach ($strategies as $strategy) {

            //print_r($strategy->StrategyConcept);

            foreach ($strategy->StrategyConcept as $concept) {
                $concepts[]=$concept->concept_id;
            }
        }

        //return count($concepts);

        //die();

        //Strategies Concepts
        $campaigns = Campaign::all();

        //Concepts
        //$concepts = Concept::all();

        //Creatives
        $creatives = Creative::whereIn('concept_id',$concepts)->get();

        //Domains

        //Countries
        $countries = IabCountry::all();

        //Regions
        $regions = IabRegion::all();


        return Voyager::view('voyager::vwireports', compact('reports','from','until','campaigns','concepts','creatives','countries','regions'));
    }
}