<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Strategy;
use App\Http\Resources\StrategyV2 as StrategyResource;

class StrategyControllerV2 extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id>1000000){
          $float_wlprefix = env('WL_PREFIX').".0";
          $wlprefix = (float) $float_wlprefix*1000000;
          $old_id = $id;
          $id = $id-$wlprefix;
          $prefix = $old_id - $id;
          //die($id);
          if($id>=1000000){
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
            die();
          }
        }
        $strategy = Strategy::with("StrategiesLocationsCity","StrategiesLocationsRegion","StrategiesLocationsCountry","StrategiesPmp","StrategiesSitelist.Sitelist","StrategiesIplist.Iplist","StrategiesSsp","StrategiesTechnologiesBrowser","StrategiesTechnologiesDevice","StrategiesTechnologiesIsp","StrategiesTechnologiesOs","StrategiesInventoryType","StrategiesGeofencing")
          ->where('id','=',$id)
          ->first();
          return new StrategyResource($strategy);
    }

    public function duplicate(Request $request, $id)
    {
      if (Strategy::with('id', $id)->exists())
      {
        $STATUS = 0;
        DB::statement("
          INSERT INTO strategies (campaign_id, name, status, channel, date_start, date_end, budget, goal_type, goal_values, pacing_monetary, pacing_impression, frequency_cap)
          SELECT campaign_id, name, '$STATUS', channel, date_start, date_end, budget, goal_type, goal_values, pacing_monetary, pacing_impression, frequency_cap
          FROM strategies
          WHERE id='$id';
        ");
        $new_id = Strategy::latest()->first()->id;
        DB::statement("
          INSERT INTO strategies_concepts (strategy_id, concept_id)
          SELECT '$new_id', concept_id
          FROM strategies_concepts
          WHERE strategy_id='$id';
        ");
        DB::statement("
          INSERT INTO strategies_custom_datas (strategy_id, custom_datas, inc_exc)
          SELECT '$new_id', custom_datas, inc_exc
          FROM strategies_custom_datas
          WHERE strategy_id='$id';
        ");
        DB::statement("
          INSERT INTO strategies_data_pixels (strategy_id, pixels, inc_exc)
          SELECT '$new_id', pixels, inc_exc
          FROM strategies_data_pixels
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_geofencings (strategy_id, geolocation, inc_exc)
          SELECT '$new_id', geolocation, inc_exc
          FROM strategies_geofencings
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_inventory_types (strategy_id, inventory_type, inc_exc)
          SELECT '$new_id', inventory_type, inc_exc
          FROM strategies_inventory_types
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_iplists (strategy_id, iplist_id, inc_exc)
          SELECT '$new_id', iplist_id, inc_exc
          FROM strategies_iplists
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_locations_cities (strategy_id, city, inc_exc)
          SELECT '$new_id', city, inc_exc
          FROM strategies_locations_cities
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_locations_countries (strategy_id, country, inc_exc)
          SELECT '$new_id', country, inc_exc
          FROM strategies_locations_countries
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_locations_regions (strategy_id, region, inc_exc)
          SELECT '$new_id', region, inc_exc
          FROM strategies_locations_regions
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_pmps (strategy_id, pmp_id, inc_exc)
          SELECT '$new_id', pmp_id, inc_exc
          FROM strategies_pmps
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_segments (strategy_id, segment_id, inc_exc)
          SELECT '$new_id', segment_id, inc_exc
          FROM strategies_segments
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_sitelists (strategy_id, sitelist_id, inc_exc)
          SELECT '$new_id', sitelist_id, inc_exc
          FROM strategies_sitelists
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_ssps (strategy_id, ssp_id, inc_exc)
          SELECT '$new_id', ssp_id, inc_exc
          FROM strategies_ssps
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_technologies_browsers (strategy_id, browser_id)
          SELECT '$new_id', browser_id
          FROM strategies_technologies_browsers
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_technologies_devices (strategy_id, device_id)
          SELECT '$new_id', device_id
          FROM strategies_technologies_devices
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_technologies_isps (strategy_id, isp_id)
          SELECT '$new_id', isp_id
          FROM strategies_technologies_isps
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_technologies_oss (strategy_id, os, inc_exc)
          SELECT '$new_id', os, inc_exc
          FROM strategies_technologies_oss
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_ziplists (strategy_id, ziplist_id, inc_exc)
          SELECT '$new_id', ziplist_id, inc_exc
          FROM strategies_ziplists
          WHERE strategy_id='$id';
        ");
        DB::statement("
        INSERT INTO strategies_langs (strategy_id, lang, inc_exc)
          SELECT '$new_id', lang, inc_exc
          FROM strategies_langs
          WHERE strategy_id='$id';
        ");
        return $id;
      }
    }

    public function update(Request $request, $id)
    {
      if (Strategy::with('id', $id)->exists())
      {
        // Strategy hasOne Geofencing
        $name = $request->input('name');
        if (!empty($name)) {
          // name
          $data = $request->input('name');
          DB::statement("
          INSERT INTO strategies (strategy_id, name) VALUES($id, '$data') ON DUPLICATE KEY UPDATE    
          name='$data';
          ");
        }
        // Strategy hasOne Geofencing
        $geofencing = $request->input('geofencing');
        if (!empty($geofencing)) {
          // geolocation: Mediumtext, nullable
          $data = $request->input('geofencing.data');
          // inc_exc: Tinyint, nullable
          $inc_exc = $request->input('geofencing.inc_exc');
          DB::statement("
            INSERT INTO strategies_geofencings (strategy_id, geolocation, inc_exc) VALUES($id, 'arr', '$inc_exc') ON DUPLICATE KEY UPDATE    
            geolocation='arr', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasOne InventoryType
        $inventory_types = $request->input('inventory_types');
        if (!empty($inventory_types)) {
          // inventory_type: Varchar
          $data = $request->input('inventory_types.data');
          // inc_exc: Integer, nullable
          $inc_exc = $request->input('inventory_types.inc_exc');
          DB::statement("
          INSERT INTO strategies_inventory_types (strategy_id, inventory_type, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          inventory_type='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategies hasMany Sitelist
        $sitelists = $request->input('sitelists');
        if (!empty($sitelists)) {
          // sitelist_id: Integer, not null
          $data = $request->input('sitelists.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('sitelists.inc_exc');
          DB::statement("
          INSERT INTO strategies_sitelists (strategy_id, sitelist_id, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          sitelist_id='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasMany Iplist
        $iplists = $request->input('iplists');
        if (!empty($iplists)) {
          // iplist_id: Integer, not null
          $data = $request->input('iplists.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('iplists.inc_exc');
          DB::statement("
          INSERT INTO strategies_iplists (strategy_id, pmp_id, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          iplist_id='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasMany PMPs
        $pmps = $request->input('pmps');
        if (!empty($pmps)) {
          // pmp_id: Varchar, not null
          $data = $request->input('pmps.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('pmps.inc_exc');
          DB::statement("
          INSERT INTO strategies_pmps (strategy_id, pmp_id, inc_exc) VALUES($id, '$data', $inc_exc) ON DUPLICATE KEY UPDATE    
          pmp_id='$data', inc_exc=$inc_exc;
          ");
        }

        // Strategy hasMany SSPS
        $ssps = $request->input('ssps');
        if (!empty($ssps)) {
          // ssp_id: Varchar, not null
          $data = $request->input('ssps.data');
          DB::statement("
          INSERT INTO strategies_ssps (strategy_id, ssp_id) VALUES($id, '$data') ON DUPLICATE KEY UPDATE    
          ssp_id='$data';
          ");
        }

        // Strategy hasMany Ziplists
        $ziplists = $request->input('ziplists');
        if (!empty($ziplists)) {
          // ziplist_id: Integer, not null
          $data = $request->input('ziplists.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('ziplists.inc_exc');

          DB::statement("
            INSERT INTO strategies_ziplists (strategy_id, ziplist_id, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
            ziplist_id='$data', inc_exc='$inc_exc';
            ");
        }

        // Strategy hasOne Country
        $countries = $request->input('countries');
        if (!empty($countries)) {
          // country: Text
          $data = $request->input('countries.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('countries.inc_exc');
          DB::statement("
          INSERT INTO strategies_locations_countries (strategy_id, country, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          country='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasOne Region
        $regions = $request->input('regions');
        if (!empty($regions)) {
          // region: Text
          $data = $request->input('regions.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('regions.inc_exc');
          DB::statement("
          INSERT INTO strategies_locations_regions (strategy_id, region, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          region='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasOne City
        $cities = $request->input('cities');
        if (!empty($cities)) {
          // city: Text
          $data = $request->input('cities.data');
          // inc_exc: Integer, not null
          $inc_exc = $request->input('cities.inc_exc');
          DB::statement("
          INSERT INTO strategies_locations_cities (strategy_id, city, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          city='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasOne Device
        $devices = $request->input('devices');
        if (!empty($devices)) {
          // device_id: Varchar, not null
          $data = $request->input('devices.data');
          DB::statement("
          INSERT INTO strategies_technologies_devices (strategy_id, device_id) VALUES($id, '$data') ON DUPLICATE KEY UPDATE    
          device_id='$data';
          ");
        }

        // Strategy hasOne ISP
        $isps = $request->input('isps');
        if (!empty($isps)) {
          // isp_id: Varchar, not null
          $data = $request->input('isps.data');
          DB::statement("
          INSERT INTO strategies_technologies_isps (strategy_id, isp_id) VALUES($id, '$data') ON DUPLICATE KEY UPDATE    
          isp_id='$data';
          ");
        }

        // Strategy hasOne OS
        $oss = $request->input('oss');
        if (!empty($oss)) {
          // os: Varchar
          $data = $request->input('oss.data');
          // inc_exc: Tinyint, not null
          $inc_exc = $request->input('oss.inc_exc');
          DB::statement("
          INSERT INTO strategies_technologies_oss (strategy_id, os, inc_exc) VALUES($id, '$data', '$inc_exc') ON DUPLICATE KEY UPDATE    
          os='$data', inc_exc='$inc_exc';
          ");
        }

        // Strategy hasOne Browser
        $browser = $request->input('browser');
        if (!empty($browser)) {
          // browser_id: Varchar, not null
          $data = $request->input('browser.data');
          DB::statement("
          INSERT INTO strategies_technologies_browsers (strategy_id, browser_id) VALUES($id, '$data') ON DUPLICATE KEY UPDATE    
          browser_id='$data';
          ");
        }

      }
      return 'Done';
    }
}
