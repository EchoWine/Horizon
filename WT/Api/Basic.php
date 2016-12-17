<?php

namespace WT\Api;

use Cfg;
use CoreWine\Http\Client;


class Basic{

	/**
	 * Name of api
	 *
	 * @param string
	 */
	protected $name;

	/**
	 * List of all resources that this api can retrieve
	 * 
	 * @var Array (series|anime|manga)
	 */
	protected $resources = [];

	/**
	 * Basic api url
	 *
	 * @param string
	 */
	protected $url;


	/**
	 * Construct
	 */
	public function __construct(){}

	/**
	 * Get name of api
	 *
	 * @return string
	 */
	public function getName(){
		return $this -> name;
	}

	/**
	 * Can i research this type of resource?
	 *
	 * @param string $resource
	 *
	 * @return bool
	 */
	public function isResource($resource){
		return $resource == 'all' || in_array($resource,$this -> resources);
	}

	/**
	 * Request a discovery
	 *
	 * @param array $params
	 */
	public function discoveryRequest($params){}

	/**
	 * Discovery a resource
	 *
	 * @param string $key
	 */
	public function discovery($key){}


	/**
	 * Download temp image
	 */
	public function download($source,$to){

		$final_poster = Cfg::get('app.host')."/src/WT/assets/img/default_200x281.jpg";

		if(empty($source))
			return $final_poster;


		$client = new Client();

		$destination = Cfg::get('app.drive').Cfg::get('app.public')."uploads/{$to}";

		if(!file_exists(dirname($destination)))
			mkdir(dirname($destination),0777,true);

		if(!file_exists($destination))
			$client -> download($source,$destination);
		

		$final_poster = Cfg::get('app.host').Cfg::get('app.root')."uploads/{$to}";
	
		return $final_poster;
	}




}