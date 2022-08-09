<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IabCountry;
use Google\Cloud\Storage\StorageClient;

class GoogleStorage extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $args =
            [
                'projectId'     => "media-storm-245600",
                'keyFilePath'   => "/var/www/html/dsp/public/lab/sito/SITO Data-78e41320578c.json",
            ];

        $storage = new StorageClient($args);
        foreach ($storage->buckets() as $bucket) {
            printf('Bucket: %s' . PHP_EOL, $bucket->name());
        }
        //return "Hola";

    }

}