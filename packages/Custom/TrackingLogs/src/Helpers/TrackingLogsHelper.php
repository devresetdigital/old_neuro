<?php

namespace Custom\TrackingLogs\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TrackingLogsHelper {

	static $channel = 'tracking';
	static $allowed_types = [
		'emergency',
		'alert',
		'critical',
		'error',
		'warning',
		'notice',
		'info',
		'debug'
	];


	/**
	 *  AGREGAR TODAS LAS RUTAS QUE NECESITEN SER LOGUEADAS DE LA SIGUIENTE MANERA:
	 * 
	 * 	ej: edit/campaign/23 es un POST  = 'edit/campaign/{var}=>POST' 
	 * 	las variables son reemplazadas por {var}
	 * 
	 * 
	 * 	ej: un post a login = 'admin/login=>POST'
	 */
	 static $paths = [
		'admin/login=>POST',
		//'admin/campaigns=>POST',
		//'admin/campaigns/order=>POST',
		//'admin/strategies=>POST',
		//'admin/strategies/order=>POST',
		//'admin/concepts=>POST',
		//'admin/concepts/order=>POST',
		//'admin/creatives=>POST',
		//'admin/creatives/order=>POST',
		//'admin/users=>POST',
		'admin/users-advertisers=>POST',
		'admin/users-advertisers/order=>POST',
		//'admin/users/order=>POST',
		//'admin/sitelists=>POST',
		//'admin/sitelists/order=>POST',
		//'admin/ziplists=>POST',
		//'admin/ziplists/order=>POST',
		//'admin/conversion-pixels=>POST',
		//'admin/conversion-pixels/order=>POST',
		//'admin/pixels=>POST',
		//'admin/pixels/order=>POST',
		//'admin/vwis=>POST',
		//'admin/vwis/order=>POST',
		
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/campaigns/{var}=>PUT',
		'admin/advertisers/{var}=>PUT',
		'admin/blacklists/{var}=>PUT',
		'admin/concepts/{var}=>PUT',
		'admin/conversion-pixels/{var}=>PUT',
		'admin/creatives/{var}=>PUT',
		'admin/organizations/{var}=>PUT',
		'admin/pixels/{var}=>PUT',
		'admin/pmps/{var}=>PUT',
		'admin/sitelists/{var}=>PUT',
		'admin/strategies/{var}=>PUT',
		'admin/users-advertisers/{var}=>PUT',
		'admin/users/{var}=>PUT',
		'admin/vwi-locations/{var}=>PUT',
		'admin/vwis/{var}=>PUT',
		'admin/ziplists/{var}=>PUT'
	];

	static $fieldsBlackList = [
		'password'
	];


	static function checkPath($path, $method){
		$path_words = explode("/", $path);
		$path_to_check=[];
		foreach ($path_words as $word){
			$path_to_check[] = (ctype_digit($word)) ? '{var}' : $word;
		}
		$path_words = implode('/', $path_to_check) . '=>'.$method;

		return in_array($path_words, self::$paths);
	}



	/**
	 *
	 * @param type $response
	 * @return string
	 */
	static function getType($response) {

		$status_code = $response->getStatusCode();

		switch ($status_code) {
			case ($status_code >= 500):
				return 'error';
				break;
			default:
				return 'info';
				break;
		}
	}

	/**
	 *
	 * @param type $request
	 * @param type $response
	 * @return boolean
	 */
	static function logAction($request, $response = null) {

	

		$path = $request->path();

		if(!self::checkPath($path, $request->getMethod())){
			return true;
		}

		$url = $request->url();
		$userEmail = null;

		$type= self::getType($response);

		if (Auth::check()) {
			$userEmail = Auth::user()->email;
		}

		$request_body = self::getRequestBody($request);

		foreach (self::$fieldsBlackList as $key => $value) {
			if (array_key_exists($value, $request_body)){
				$request_body[$value] = '******';
			}
		}
		if(array_key_exists('_validate', $request_body)) {
			return true;
		}

		$toLog=json_encode([
			'type' => $type,
			'user_email' => $userEmail,
			'method' => $request->getMethod(),
			'path' => $path,
			'url' => $url,
			'request' =>$request_body ,
			'client_ip' => $request->getClientIp(),
			'status_code' => $response->getStatusCode(),
			'date' => strtotime('now'),
            'wlid' => $_ENV["WL_PREFIX"],
		], JSON_OBJECT_AS_ARRAY);

		if($_ENV['SAVE_USER_ACTIVITY']){
			//Send Log to log server
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL,            "http://104.131.90.165:9000" );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt($ch, CURLOPT_POST,           1 );
			curl_setopt($ch, CURLOPT_POSTFIELDS,     $toLog );
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			//curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:','Content-Type: text/plain'));

			$result = curl_exec($ch);
		}

		Log::channel(self::$channel)->$type(
			$toLog
		);
		return true;
	}

	static function getRequestBody($request){
		$keys = $request->keys();
		$body=[];
		foreach ($keys as $key) {
			$body[$key] = self::object_to_array($request->$key);
		}
		return $body;
	}

	static function object_to_array($obj) {
		if (is_object($obj))
			$obj = (array) self::dismount($obj);
		if (is_array($obj)) {
			$new = array();
			foreach ($obj as $key => $val) {
				$new[$key] =self::object_to_array($val);
			}
		} else
			$new = $obj;
		return $new;
	}

	static function dismount($object) {
		$reflectionClass = new \ReflectionClass(get_class($object));
		$array = array();
		foreach ($reflectionClass->getProperties() as $property) {
			$property->setAccessible(true);
			$array[$property->getName()] = $property->getValue($object);
			$property->setAccessible(false);
		}
		return $array;
	}

}
