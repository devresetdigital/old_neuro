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
use App\User;
use App\Advertiser;
use App\UsersAdvertiser;
use App\ConversionPixel;
use App\Pixel;

class ReportsatController extends Controller
{

    public function index()
    {

        //return Auth::user();

        //GET REPORT CONTENT BY DATE
        /* $report_json = file_get_contents("http://e-us-east01.resetdigital.co:8080/0_impressions?groupby=date&from=18100100&until=18100723&format=json");

         $reports = "[";
         foreach(json_decode($report_json,true) as $key => $val){
             //formaat date
             $date_year = substr($key,0,2);
             $date_month = substr($key,2,2);
             $date_day = substr($key,4,2);

             //By Date Report
             $date = $date_month."-".$date_day."-".$date_year;
             $impressions = $val[1];
             $clics = $val[4];
             $spent = $val[3]!=0 ? round($val[3]/1000,2) : 0;
             $ecpm = ($val[1]!=0 && $val[1] !=0) ? round($val[1]/$val[3],2) : 0;
             $cpc = ($val[3]!=0 && $val[4]!=0) ? round(($val[3]/1000)/$val[4],2) : 0;
             $ctr = ($val[1] != 0 && $val[4]!= 0)? round(($clics*100)/$impressions,2) : 0;
             $conversions = $val[9];
             $cpa = ($val[3]!=0 && $val[9]!=0) ? round(($val[3]/1000)/$val[9],2) : 0;

             $reports.= "['$date',"; //DATE
             $reports.= $impressions.","; // IMPRESSIONS
             $reports.= $clics.","; // CLICKS
             $reports.= $spent.","; // SPENT
             $reports.= $ecpm.",";  // ECPM
             $reports.= $ctr.","; //CTR
             $reports.= $cpc.","; //CPC
             $reports.= $conversions.","; //CONVERSIONS
             $reports.= $cpa."],"; //CPA
         }
         $reports.="]";*/

        $from = Carbon::now()->subDays(6)->format("ymdH");
        $until = Carbon::now()->format("ymdH");
        //Organization
        $userorganization="";
        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 5) {
            $logged_user = User::where('id', '=', Auth::user()->id)->get();
            $userorganization = $logged_user[0]->organization_id;
        }

        $user_id = Auth::user()->id;
        $user_role = Auth::user()->role_id;

        if(Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 5){

            //Campaigns
            $logged_user = User::where('id', '=', Auth::user()->id)->get();
            //Get Advertisers From Organization
            $advertisers = Advertiser::where('organization_id', '=', $logged_user[0]->organization_id)->get();

            //return $advertisers;

            //return $advertisers[1]->id;
            $advids = array();
            foreach ($advertisers as $adv){
                $advids[]=$adv->id;
            }
            //die($user_role);
            if($user_role == 5){
                $user_advertiser = UsersAdvertiser::where('user_id','=',$user_id)->get();
                $advids = array();
                $advids = array($user_advertiser[0]->advertiser_id);
            }
            $campaigns = Campaign::whereIn('advertiser_id',$advids)->get();
            $concepts = Concept::whereIn('advertiser_id',$advids)->get();
            $creatives = Creative::whereIn('advertiser_id',$advids)->get();

            //$campaigns = Campaign::where-("","","")->get();

            //Concepts
            //$concepts = Concept::all();

            //Creatives
            //$creatives = Creative::all();

        } else {

            //Campaigns
            $campaigns = Campaign::all();

            //Concepts
            $concepts = Concept::all();

            //Creatives
            $creatives = Creative::all();
        }


        //Domains

        //Countries
        $countries = IabCountry::all();

        //Regions
        $regions = IabRegion::all();

        return Voyager::view('voyager::reports_at', compact('reports','from','until','campaigns','concepts','creatives','countries','regions','userorganization','user_id','user_role'));
    }


    public function reportsEmbeded()
    {
        switch ($_ENV['WL_PREFIX']) {
            case 1:
                $iframe_new = "http://uirst.resetdigital.co/reports/wl_1_impressions ";
                break;
            case 2:
                $iframe_new = "http://uirst.resetdigital.co/reports/wl_2_impressions ";
                break;
            case 3:
                $iframe_new = "http://uirst.resetdigital.co/reports/wl_3_impressions ";
                break;
            default:
                $iframe_new = "http://uirst.resetdigital.co/reports/" ;
                break;
        }

        return Voyager::view('voyager::reportsEmbeded', compact('iframe_new'));
    }



    public function dailyReport()
    {
        return Voyager::view('voyager::dailyreport');
    }

    public function reportUpdated($type, $id){

        switch ($type) {
            case 'smartpixel':
                $report = Pixel::find(intval($id));
                break;
            case 'attribution':
                $report = ConversionPixel::find(intval($id));
                break;
            default:
                return response()->json('invalid type',401);
                break;
        }
        if($report==null){
            return response()->json('invalid report ID',401);
        }

        $report->last_update = now();
        $report->save();

        return response()->json($report->toArray(),200);

    }


}