<?php

namespace App\Http\Middleware;

use Closure;
use Custom\TrackingLogs\Helpers\TrackingLogsHelper;
use Log;
use Illuminate\Support\Facades\Auth;

class Logs {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next) {
		return $next($request);
	}

	public function terminate($request, $response) {
		$this->log($request, $response);
	}


	protected function log($request, $response) {
		TrackingLogsHelper::logAction(
		$request,
		$response);
	}

}
