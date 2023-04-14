<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//List Campaigns
Route::get('campaigns','CampaignController@index');
//List Campaigns
Route::get('campaigns_ranges','CampaignController@campaignRanges');

//List Single Campaign
Route::get('campaigns/{id}','CampaignController@show');

//List Campaign vwis
Route::get('campaigns/{id}/vwis','CampaignController@vwis');

//List Campaigns With vwis
Route::get('campaigns_wvwis','CampaignController@withvwis');

//list of campaigns by advertiser
Route::get('campaigns_by_advertiser/{id}','CampaignController@getByAdvertiser');

//List Single Concept
Route::get('concepts/{id}','ConceptController@show');
Route::get('concepts','ConceptController@index');
Route::post('concepts_by_advertiser','ConceptController@getByAdvertiser');

// Advertisers
Route::get('advertisers','AdvertisersController@index');



//bulk actions
Route::post('check_existance','ConceptController@checkExistance');
Route::post('check_existance_campaigns','CampaignController@checkExistance');

Route::post('execute_bulk_creatives','CreativeController@bulkStore');
Route::post('execute_bulk_strategies','StrategyController@bulkStore');

//List Single Concept
Route::get('creatives','CreativeController@index');
Route::get('creatives/{id}','CreativeController@show');

Route::get('vast_preview/{id}','CreativeController@vastPreview');
Route::post('save_vast_markup','CreativeController@saveVastMarkup');

//List strategy detail
Route::get('strategies','StrategyController@index');
Route::get('strategies/{id}','StrategyController@show');

Route::get('strategies_lists','StrategyController@strategiesLists');

//List strategy geofencing
Route::get('strategies/{id}/geofencing','StrategyController@geofencing');

//List Single Sitelist
Route::get('sitelists/{id}','SitelistController@show');

//List Single Iplist
Route::get('iplists/{id}','IplistController@show');

//List Single Publist
Route::get('publisherlists/{id}','PublisherlistController@show');

//List dmpinfo
Route::get('dmpinfo','StrategyController@dmpinfo');
Route::get('contextualinfo','StrategyController@contextualinfo');

//conversion pixel
Route::get('conversionpixels','ConversionPixelController@index');
Route::get('conversionpixels/{id}','ConversionPixelController@show');


//List Single Custom Data
Route::get('custom_data/{id}','CustomDataController@show');

//List Single VWI
Route::get('vwis/{id}','VwiController@show');

//List Single ZipList
Route::get('ziplists/{id}','ZiplistController@show');

//List Single keywords
Route::get('keywordslists/{id}','keywordslistsController@show');

//List Ssps
Route::get('ssps/','SspController@index');
Route::get('ssps_by_advertiser/{advertiser}', 'SspController@byAdvertiser');

//HBreports
Route::post('hbreports','HBreportsController@index');

//HBreports
Route::post('inreports','InventoryReportsController@index');

//HBreports
Route::post('pxreports','InventoryReportsController@pixels');

Route::post('pixel_analytics_report','InventoryReportsController@pixelsAnalyticsReport');
Route::post('pixel_analytics_report_table','InventoryReportsController@pixelsAnalyticsReportTable');

Route::post('pixel_conversion_report','InventoryReportsController@pixelsConversionReport');
Route::post('pixel_conversion_report_table','InventoryReportsController@pixelsConversionReportTable');

//app domain quality

Route::post('app_domain_quality_table','QualityController@appDomainQualityTable');
//pixels
Route::get('pixels','PixelsController@index');

//conversion pixels
Route::get('conversion_pixels','PixelsConvertionController@index');

//Cities
Route::get('cities','IabCityController@index');

//Organizations
Route::get('organizations','OrganizationController@index');

//Organizations
Route::get('strategiesactive','StrategyActiveController@index');


//blocklist
Route::get('blocklists/type/{type}','BlocklistController@getByType');

//Crons
Route::get('crons','CronController@index');
Route::get('get-fou-data', 'CronController@fouData');


//--------------------------TMT INTEGRATION----------------------------------------------
Route::get('trust_scan_cron','CronController@trustScan');
Route::get('trust_scan_cron_daily','CronController@TrustScanDaily');
Route::get('send_for_bulk_scan','CreativeController@sendForBulkScan');
//------------------------------------------------------------------------


//Crons
Route::get('crons2','CronController@index');

//Cron
Route::get('cron_budget','CronBudgetController@index');

//rsn
Route::get('rsn_resonance_by_daypart/{id}','RsnController@getByDaypart');
Route::get('rsn_needstates_by_ad/{id}','RsnController@getNeedstateByAd');
Route::post('rsn_resonance','RsnController@getResonance');
Route::post('rsn_resonance_paginated','RsnController@getResonancePaginated');

Route::get('rsn_get_campaigns_by_advertiser','RsnController@getByAdvertiser');
Route::get('rsn_get_signal_campaigns_by_advertiser','RsnController@getSignalByAdvertiser');

Route::post('rsn_get_top_networks','RsnController@getTopNetworks');
Route::post('rsn_get_top_programs','RsnController@getTopPrograms');
Route::post('rsn_get_top_ad','RsnController@getTopAd');

Route::post('rsn_resonance_by_network','RsnController@getByNetwork');

Route::post('rsn_campaign','RsnController@createCampaign');
//rsn SIGNALS

Route::get('get_signals_by_campaign/{id}','RsnSignalReports@getSignalsByCampaign');
Route::get('get_neuro_campaign/{id}','RsnSignalReports@getCampaign');
Route::get('get_domains_by_item/{id}','RsnSignalReports@get_domains_by_item');




Route::get('sitelists','SitelistController@index');
Route::get('iplists','IplistController@index');
Route::get('publisherlists','PublisherlistController@index');
Route::get('custom_data','CustomDataController@index');
Route::get('ziplists','ZiplistController@index');
Route::get('keywordslists','keywordslistsController@index');


//List Campaigns
Route::get('get_real_time_spent','CampaignController@getRealTime');
//PI
Route::post('get_path_interactive_data','CampaignController@getPathInteractiveData');
//
Route::post('daily_report_table','CampaignController@dailyReportTable');
//EXPORTS
Route::get('exports/{type}','ExportsController@exports');

Route::post('rsn_ad','RsnController@createAd');
Route::post('rsn_ad_resonance/{adId}','RsnController@fillAdResonance');
Route::post('rsn_delete_ad/{adId}','RsnController@deleteAd');
Route::get('rsn_get_campaign/{id}','RsnController@getCampaign');
Route::get('rsn_calculate_prediction/{id}','RsnController@calculatePrediction');
Route::get('exportPacing','CampaignController@exportPacing');
// API V2
Route::get('v2/campaigns','CampaignControllerV2@index');
Route::get('v2/campaigns/{id}','CampaignControllerV2@show');
Route::get('v2/strategies_by_campaign/{id}','CampaignControllerV2@strategies_by_campaign');
Route::get('v2/strategies/{id}','StrategyControllerV2@show');
Route::put('v2/strategies/{id}','StrategyControllerV2@update');
Route::post('v2/strategies/{id}/duplicate','StrategyControllerV2@duplicate');




Route::post('upload_to_s3','AmazonController@upload');
Route::get('show_files_s3','AmazonController@index');


Route::get('report_updated/{type}/{id}','ReportsController@reportUpdated');


/////////////////////////////////////////////////////////
Route::post('campaigns/changeStatus/{id}','CampaignController@changeStatus');
Route::post('strategies/changeStatus/{id}','StrategyController@changeStatus');
Route::post('creatives/changeStatus/{id}','CreativeController@changeStatus');



////////////////////////////////////////////////////////////
Route::post('upload','UploaderController@postUpload');
Route::post('publicLinkUpload','UploaderController@publicLinkUpload');

Route::get('upload','UploaderController@postUpload');

////////////////////MISELANIUS////////////////////////////////////////
Route::post('make_request','Controller@makeRequest');
////////////////////MISELANIUS////////////////////////////////////////
Route::post('make__get_request','Controller@makeGetRequest');

Route::get('get_bid/{id}','Controller@getBid');
Route::post('get_bid/{id}','Controller@getBid');