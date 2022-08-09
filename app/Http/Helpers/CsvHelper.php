<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Response;

class CsvHelper
{
    /**
     * converts an array into a csv file 
     */
    static function getCsv(Array $data, $filename = 'export'){

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=" . $filename . '.csv',
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        ob_end_clean();
        $callback = function() use ($data)
        {
            foreach($data as $row) {
                foreach($row as &$line) {
                    $aux = str_replace('%', '', $line);
                    $aux = str_replace(' ', '', $aux);
                    if(strpos($line,'%') !== false && is_numeric($aux)) {
                        $line =  floatval($aux)/100;
                    }
                }
                $file = fopen('php://output', 'w');
                fputcsv($file, $row);
                fclose($file);
            }
      
        };
        return Response::stream($callback, 200, $headers);
    }


}
