<?php

namespace App\Http\Controllers;

use TCG\Voyager\Facades\Voyager;
use Illuminate\Http\Request;

class RsnImporterController extends Controller
{
    /**
     *
     */
    public function index()
    {
        if(isset($_GET["csv"])){
           // $csv = file_get_contents("http://dsp.resetdigital.co/rsncsvs/".$_GET["csv"]);
            $row = 1;
            if (($handle = fopen("http://dsp.resetdigital.co/rsncsvs/".$_GET["csv"], "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    echo "<p> $num fields in line $row: <br /></p>\n";
                    $row++;
                    for ($c=0; $c < $num; $c++) {
                        echo $data[$c] . "<br />\n";
                    }
                }
                fclose($handle);
            }
            //return $csv;
        } else {
            return "hola";
        }
    }

}