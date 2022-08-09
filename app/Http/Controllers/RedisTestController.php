<?php

namespace App\Http\Controllers;

$_ENV["REDIS_HOST"]="172.106.112.170";
$_ENV["REDIS_PASSWORD"]="bHpvXUfFr5Q-h0aGlOkb5NSs61_Gjf_UfedbHfTyP-Hejwk8WmCqX6q6WDyAsak_b7nkYXu3N6VTM_xYKiE6yH6UteNcN-E3dis8lxpBaApQrxTZCbzOHwc8TyfBt_2F7QViDOzaEPjhC7mN0fEfO3s_UPy.3vSdUxV7snpuDoyYw_aS0-OB6TbUfw8BoFV1PkfOosrod3HP9Rg9U6INYQw9bxs6bke2DgZ3xdX3mT0_FjzjohQykUizt2f2sKvA";

use App\Campaign;
use App\CampaignsBudgetFlight;
use App\Strategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Carbon;

class RedisTestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $rediskey = Redis::connection("remote")->keys("*");
        print_r($rediskey);

    }
}