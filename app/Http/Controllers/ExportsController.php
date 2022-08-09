<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CsvHelper;

class ExportsController extends Controller
{
    public function exports($type){
        return $this->{$type}();
        try {
            return $this->{$type}();
        } catch (\Throwable $th) {
            return "invalid export type";
        }
    }


    private function pi(){

        if(!array_key_exists('campaign_id', $_GET)){
            return 'missing argument campaign_id';
        }
        if(!array_key_exists('type', $_GET)){
            return 'missing argument type';
        }
        if(!array_key_exists('context', $_GET)){
            return 'missing argument context';
        }
        if(!array_key_exists('search', $_GET)){
            return 'missing argument search';
        }
        $filename = 'Audience Report';

        $url = env('APP_URL').'/api/get_path_interactive_data';
        //The data you want to send via POST
        $fields = [
            'campaign_id'      => $_GET['campaign_id'],
            'type' => $_GET['type'],
            'context'         => $_GET['context'],
            'search'=> ['value' =>$_GET['search'] ]
        ];

        $filename .= '- campaign ' . $_GET['campaign_id'];

        if(array_key_exists('strategy_id', $_GET)){
            $fields['strategy_id'] = $_GET['strategy_id'];
            $filename .= '- strategy ' . $_GET['strategy_id'];
        }

        $response = $this->httpPost($url, $fields);

        $data = $response['data'];

        if(!is_array($data)){
            $data=[];
        }
        array_unshift($data, ['Name','Count','Percentage']);
     
        return CsvHelper::getCsv($data,$filename);
    }


    private function daily_report(){
      
        if(!array_key_exists('groupby', $_GET)){
            return 'missing argument groupby';
        }
        if(!array_key_exists('campaigns', $_GET)){
            return 'missing argument campaigns';
        }
        if(!array_key_exists('from', $_GET)){
            return 'missing argument from';
        }
     
        $filename = 'Daily Report - ' . $_GET['from'] . ' - ';
        
        $filename .= str_replace(',',' - ', $_GET['campaigns'] );

        $url = env('APP_URL').'/api/daily_report_table';
        //The data you want to send via POST
        $fields = [
            'campaigns'      => $_GET['campaigns'] == 'all' ? null:  explode(',',$_GET['campaigns']),
            'from'           => $_GET['from'],
            'until'          => $_GET['until'],
            'includeid'      => $_GET['includeid'],
            'groupby'        => $_GET['groupby'],
            'format'         => 'json'
        ];
        

        $response = $this->httpPost($url, $fields);

        if(!is_array($response)){
            $response=[];
        }

        $labels = explode( ',',$_GET['groupby']);
        $labels[]='impresions';
        $labels[]='spent';
        $labels[]='ecpm';
        $labels[]='cliks';
        $labels[]='ctr';
        $labels[]='Start';
        $labels[]='FirstQ';
        $labels[]='Middle';
        $labels[]='ThirdQ';
        $labels[]='Complete';
        array_unshift($response, $labels);
     
        return CsvHelper::getCsv($response,$filename);

    }

    private function pixel_analytics() {

        $url = env('APP_URL').'/api/pixel_analytics_report_table';
        //The data you want to send via POST
   
        $_GET['groupby'] = implode(',',$_GET['groupBy']);

        $response = $this->httpPost($url, $_GET);

        $data = $response['data'];

        if(!is_array($data)){
            $data=[];
        }

        $filename = 'pixel analytics - pixel '.$_GET['pixelid'];

        $labels = [];
        foreach (explode( ',',$_GET['groupby']) as $iterator) {
            $iteratorLabel = '';
            switch ($iterator) {
                case 'regioncity_1':
                    $iteratorLabel = 'region';
                    break;
                case 'regioncity_2':
                    $iteratorLabel = 'city';
                    break;
                case 'deviceosbrowser_1':
                    $iteratorLabel = 'device';
                    break;
                case 'deviceosbrowser_2':
                    $iteratorLabel = 'os';
                    break;
                case 'deviceosbrowser_3':
                    $iteratorLabel = 'browser';
                    break;
                default:
                    $iteratorLabel = $iterator;
                    break;
            }
            $labels[]=$iteratorLabel;
        }
        $labels[]='hits';
        $labels[]='uniques';

        array_unshift($data, $labels);
      
        return CsvHelper::getCsv($data, $filename);
    }

    private function quality() {

        $url = env('APP_URL').'/api/app_domain_quality_table';
        //The data you want to send via POST
       
        $_GET['groupby'] = implode(',',$_GET['groupBy']);

        $response = $this->httpPost($url, $_GET);

        $data = $response['data'];

        if(!is_array($data)){
            $data=[];
        }

        $filename = 'Quality - '. $_GET['from'] .  ' - ' . $_GET['until']  ;

        $labels = [];
        foreach (explode( ',',$_GET['groupby']) as $iterator) {
            $labels[]=$iterator;
        }
        $labels[]='Viewability';
        $labels[]='eCPM';
        $labels[]='Completion Rate';
        $labels[]='Tos';
        $labels[]='Above The Fold';
        $labels[]='Ctr';
        $labels[]='Viewable';
        $labels[]='Confirmed bots';
        $labels[]='Likeable bots';
        $labels[]='Search crawlers';
        $labels[]='Fake device';
        $labels[]='Stacke ads';

        array_unshift($data, $labels);
      
        return CsvHelper::getCsv($data, $filename);
    }

    private function pixel_conversion() {

        $url = env('APP_URL').'/api/pixel_conversion_report_table';
        //The data you want to send via POST
   
        $_GET['groupby'] = implode(',',$_GET['groupBy']);
 
        $response = $this->httpPost($url, $_GET);
     
        $data = $response['data'];
        $filename = 'pixel conversion - pixel '.$_GET['pixelid'];

        if(!is_array($data)){
            $data=[];
        }

        $labels = [];
        foreach (explode( ',',$_GET['groupby']) as $iterator) {
            $iteratorLabel = '';
            switch ($iterator) {
                case 'regioncity_1':
                    $iteratorLabel = 'region';
                    break;
                case 'regioncity_2':
                    $iteratorLabel = 'city';
                    break;
                case 'deviceosbrowser_1':
                    $iteratorLabel = 'device';
                    break;
                case 'deviceosbrowser_2':
                    $iteratorLabel = 'os';
                    break;
                case 'deviceosbrowser_3':
                    $iteratorLabel = 'browser';
                    break;
                case 'campstrat_1':
                    $iteratorLabel = 'Campaign';
                    break;
                case 'campstrat_2':
                    $iteratorLabel = 'Strategy';
                    break;
                case 'campstrat_3':
                    $iteratorLabel = 'Creative';
                    break;
                default:
                    $iteratorLabel = $iterator;
                    break;
            }
            $labels[]=$iteratorLabel;
        }
        $labels[]='user conversions';
        $labels[]='ip conversion';
        $labels[]='hits';
        $labels[]='uniques';

        array_unshift($data, $labels);
      
        return CsvHelper::getCsv($data, $filename);
    }



    private function reachFrequency(){

        if(!array_key_exists('strategy_id', $_GET) && !array_key_exists('campaign_id', $_GET)){
            return 'missing argument strategy_id or campaign_id';
        }

        if(array_key_exists('strategy_id', $_GET)){
            $url = env('APP_URL').'/admin/strategies/reach_frequency/'.$_GET["strategy_id"].'?format=json';
            $filename = 'R&F - strategy ' . $_GET["strategy_id"];
        }else{
            $url = env('APP_URL').'/admin/reach_frequency?format=json&campaign_id='.$_GET["campaign_id"];
            $filename = 'R&F - campaign ' . $_GET["campaign_id"];
        }

        $request = file_get_contents($url);
        $data  = json_decode($request, true);
        $result = [];
        foreach($data['all'] as $date => $row){
            $result[] = array_merge( [$date],$row);
        }
        array_unshift($result, ['Date','Reach (Id)','Frecuency (Id)' ,'Reach (IP)','Frecuency (IP)']);
        return CsvHelper::getCsv($result, $filename);
    }


    private function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response,true);
    }
}
