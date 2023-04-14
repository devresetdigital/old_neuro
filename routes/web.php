<?php
use Illuminate\Support\Facades\Cache;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return redirect('/admin/login');
});
Route::get('flush', function () {
   if(Cache::flush()){
       return 'cache flushed';
   }
   return 'cannot flush the cache';
});
Route::get('iframes/domains_by_items_tables/{id}', function ($id) {
    // Lógica para obtener los datos a mostrar en la vista
    return view('iframes/domains_table', compact('id'));
});

//List Campaigns
Route::get('import_locations','ImportLocationController@index');

Route::get('google_storage','GoogleStorage@index');
// Move to Admin Group ->

Route::get('vast_tester','Voyager\CreativeController@vastTester');
// Move to Admin Group ->


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::get('daily_pacing','CampaignControllerV2@status');

    Route::get('reports_embeded','ReportsController@reportsEmbeded');
    Route::get('daily_report','ReportsController@dailyReport');
    Route::get('reports', function () {
        return redirect('/admin/X2_report');
    });
    Route::get('reportsat','ReportsatController@index')->name('reports');
    //campaign reports

    Route::get('creports','CreportsController@index');
    Route::get('reach_frequency','Voyager\CampaignController@reachFrequency');
    Route::get('special_reports','Voyager\CampaignController@specialReports');

    // strategies reports
    
    Route::get('strategies/reports/{id}','Voyager\StrategyController@reports');
    Route::get('strategies/reach_frequency/{id}','Voyager\StrategyController@reachFrequency');
    Route::get('strategies/special_reports/{id}','Voyager\StrategyController@specialReports');

    Route::get('organizations/reports/{id}','Voyager\OrganizationController@reports');
    
    Route::get('dashboard','DashboardController@index');
    Route::get('level1_report','RsnController@report');
    Route::post('level1_report','RsnController@storeRsnCampaign');
    Route::get('X2_report','RsnController@x2_report');

    Route::get('HAO-AI_Dashboard','RsnController@dashboard');



    Route::get('campaigns_statuses','CampaignController@statuses');

    Route::get('rsn_campaigns','RsnController@index');
    Route::get('rsn_new','RsnController@create');
    Route::get('bidder_statuses','Controller@bidderStatuses');
    Route::get('rsn_delete/{campaignId}','RsnController@delete');
    Route::get('rsn_edit/{campaignId}','RsnController@edit');
    Route::get('rsnimporter','RsnImporterController@index');
    Route::get('newdashboard','NewDashboardController@index');
    Route::get('inventory','InventoryAvailabilityController@index');
    Route::get('forecasting','ForecastingController@index');
    Route::get('autotask','AutotaskController@index');
    Route::get('datamanagement','DataManagementController@index');
    Route::get('linearreports','LinearReportsController@index');
    Route::get('vwireports','VwiReportsController@index');
    Route::get('preports','PReportsController@index');
    Route::get('pixel_analytics','PixelAnalytics@index');
    Route::get('pixel_conversion','PixelConversionController@index');
    Route::get('bulk_upload','BulkUpload@index');
    Route::get('redistest','RedisTestController@index');
    Route::get('strategies_campaign/{campaignId}','Voyager\StrategyController@strategiesByCampaign')->name('voyager.strategies_campaign.index');

    //quality
    Route::get('app_domain_quality','QualityController@index');

    //bulk views
    Route::get('bulk/creatives','Voyager\CreativeController@bulk');
    Route::get('bulk/strategies','Voyager\StrategyController@bulk');


    Route::get('/creatives/{id}/manual_scan','Voyager\CreativeController@manualScan');
    //export

    Route::get('/creatives_incidents','Voyager\CreativeController@incidents');
    Route::get('/creatives/{id}/export','Voyager\CreativeController@export');


    Route::get('/concepts/{id}/export','Voyager\ConceptController@export');
    Route::get('/campaigns/{id}/export','Voyager\CampaignController@export');
    Route::get('/strategies/{id}/export','Voyager\StrategyController@export');

    Route::get('test_scan','Voyager\CreativeController@testScan');
    //clone
    Route::get('cloneCampaign/{id}','Voyager\CampaignController@clone');
    Route::get('cloneStrategy/{id}','Voyager\StrategyController@clone');
    Route::get('cloneCreatives/{id}','Voyager\CreativeController@clone');
    Route::get('cloneConcepts/{id}','Voyager\ConceptController@clone');
    //importers


    Route::get('linear_report','Voyager\LinearController@report');
    Route::get('lineardata/remove/{id}','Voyager\LinearDataController@remove');

    Route::get('update_ssps','UpdateSspsController@index');

    Route::get('eventerstatus_v1.0', 'EventerStatusController@index');



    

});