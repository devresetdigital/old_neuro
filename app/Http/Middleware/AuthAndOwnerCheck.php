<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use App\Campaign;
use App\Strategy;
use App\Concept;
use App\Creative;
use App\Advertiser;

class AuthAndOwnerCheck {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		
		$path = $request->path();

		if($path =='admin/login'  || ! str_contains( $path, 'admin')) {
			return $next($request);
		}
		
		if(str_contains( $path, 'admin') && Auth::check()==false){
			return redirect('/admin/login');
		}

		$user = Auth::user();

		
		//check organization if the user is no and admin
		if($user && $user->role_id != 1) {
			$advertisers = Advertiser::where('organization_id',$user->organization_id)->select('id')->get()->pluck('id')->toArray();

			if((str_contains( $path, 'admin/campaigns/') && str_contains( $path, 'edit')) || str_contains( $path, 'admin/strategies_campaign') ){
				$parseUrl=explode('/',$path);
				$campaign = Campaign::find($parseUrl[2]);
				if($campaign == null || !in_array($campaign->advertiser_id, $advertisers)){
					return redirect('/admin/campaigns');
				}
			}

			if(str_contains( $path, 'admin/strategies/') && str_contains( $path, 'edit')){
				$parseUrl=explode('/',$path);
				$strategy = Strategy::find($parseUrl[2]);

				if($strategy == null){
					return redirect('/admin/campaigns');
				}
				$campaign = Campaign::find($strategy->campaign_id);
				if($campaign == null || !in_array($campaign->advertiser_id, $advertisers)){
					return redirect('/admin/campaigns');
				}
			}
			if(str_contains( $path, 'admin/concepts/') && str_contains( $path, 'edit')){
				$parseUrl=explode('/',$path);
				$concept = Concept::find($parseUrl[2]);
				if($concept == null || !in_array($concept->advertiser_id, $advertisers)){
					return redirect('/admin/concepts');
				}
			}
			if(str_contains( $path, 'admin/creatives/') && str_contains( $path, 'edit')){
				$parseUrl=explode('/',$path);
				$creative = Creative::find($parseUrl[2]);
				if($creative == null || !in_array($creative->advertiser_id, $advertisers)){
					return redirect('/admin/creatives');
				}
			}
		}


		return $next($request);
	}

	public function terminate($request, $response) {}

}
