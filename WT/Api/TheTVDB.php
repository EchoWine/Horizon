<?php

namespace WT\Api;

use WT\Api\TheTVDB as Objects;
use CoreWine\Component\Str;
use CoreWine\Http\Client;
use CoreWine\Component\File;
use Cache;
use Cfg;

class TheTVDB extends Basic{

	/**
	 * Name of api
	 *
	 * @param string
	 */
	protected $name = 'thetvdb';

	/**
	 * Token api
	 *
	 * @param string
	 */
	protected $token = '2216193F17A3C7A4';

	/**
	 * List of all resources that this api can retrieve
	 * 
	 * @var Array (series|anime|manga)
	 */
	protected $resources = ['anime','series'];

	/**
	 * Basic api url
	 *
	 * @param string
	 */
	protected $url_api = "https://www.thetvdb.com/api/";

	/**
	 * Basic api url
	 *
	 * @param string
	 */
	protected $url_public = "https://www.thetvdb.com/";

	/**
	 * Perform the request to the api in order to discovery new series
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function all($params){


		$return = [];

		$client = new Client();

		$name_request = $this -> getName()."_search_".$params['seriesname'];

		if($cache = Cache::get($name_request))
			return $cache;

		
		try{


			# Search for series
			$response = $client -> request($this -> url_api."GetSeries.php",'GET',$params);
			$resources = Str::xml($response);

		}catch(Exception $e){

			return ['error' => $e -> getMessage()];

		}
		

		if(!isset($resources -> Series))
			return $return;

		$series = $resources -> Series;
		if(!is_array($series))
			$series = [$series];

		foreach($series as $resource){

			try{

				if($resource){
					if(substr($resource -> SeriesName,0,6) == '** 403'){

						# Series blocked by policy

					}else{
						# Send request for banners
						$response = $client -> request($this -> url_api.$this -> token."/series/".$resource -> seriesid."/banners.xml");

						if(!($banners = Str::xml($response)))
							throw new \Exception();


						if(!isset($banners -> Banner))
							throw new \Exception();

						$banners = $banners -> Banner;

						if(!is_array($banners))
							$banners = [$banners];

						foreach($banners as $banner){
							if($banner -> BannerType == 'poster'){

								try{


									# Get image
									$response = $client -> request($this -> url_public."banners/".$banner -> BannerPath);

									if($response){

										# Save image
										$banner = $this -> url_public."banners/".$banner -> BannerPath;
										break;
									}else{
										$banner = '';
									}
								}catch(\Exception $e){

									$banner = '';
								}
							}
						}

						$resource = Objects\SerieObject::short($resource);


						$banner = $this -> download($banner,'thetvdb/'.$resource -> id.".jpg");


						$return[$resource -> id] = [
							'database' => $this -> getName(),
							'type' => 'series',
							'id' => $resource -> id,
							'language' => $resource -> language,
							'name' => $resource -> name,
							'poster' => $banner,
							'overview' => $resource -> overview,
							'first_aired' => $resource -> first_aired_at,
							'network' => $resource -> network,
						];
					}
			
				}
			}catch(\Exception $e){

				$banner = '';
			}

	
		}

		Cache::set($name_request,$return,3600);

		return $return;
	}

	public function get($id){

		
		$client = new Client();

		try{

			$response = $client -> request($this -> url_api.$this -> token."/series/".$id."/all/en.xml");
			$resource = Str::xml($response);

		}catch(Exception $e){

			return ['error' => $e -> getMessage()];

		}

		$o = Objects\SerieObject::long($resource);
		
		if($o -> banner)
			$o -> banner = $this -> url_public."banners/".$o -> banner;

		if($o -> poster)
			$o -> poster = $this -> url_public."banners/".$o -> poster;


		# Detect automatically if is an anime or a series, for now all are series
		$o -> type = 'series';

		return $o;
	}

	
	
	/**
	 * Discovery a resource
	 *
	 * @param string $keys
	 */
	public function discovery($key){

		return $this -> all(['seriesname' => str_replace("%20","_",$key)]);
	}


	public function update(){
		$zip_filename = Cfg::get('app.path.drive.public')."tmp/api/thetvdb/updates_day.zip";


		$xml = Cfg::get('app.path.drive.public')."tmp/api/thetvdb/";

		if(!file_exists($xml)){
			mkdir($xml,0777,true);
		}

		$client = new Client();
		$client -> download($this -> url_api.$this -> token."/updates/updates_day.zip",$zip_filename);

		$zip = new \ZipArchive;

		if($zip -> open($zip_filename) === TRUE){
		    $zip -> extractTo($xml);
		    $zip -> close();
		}

		$xml = Str::xml(file_get_contents($xml."updates_day.xml"));

		$r = [];

		foreach($xml -> Series as $serie){
			$r[] = ['id' => $serie -> id,'updated_at' => date('Y-m-d H:i:s',$serie -> time)];
		}

		return $r;
	}

}