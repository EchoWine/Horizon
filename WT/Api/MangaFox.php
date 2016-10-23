<?php

namespace WT\Api;

use CoreWine\Component\Str;
use CoreWine\Http\Client;
use CoreWine\Component\File;
use CoreWine\Component\DomDocument;
use CoreWine\Http\Request;
use Cache;
use WT\Api\MangaFox\MangaObject;
use WT\Api\MangaFox\CollectionObject;

class MangaFox extends Basic{

	/**
	 * Name of api
	 *
	 * @param string
	 */
	protected $name = 'manga-fox';

	/**
	 * List of all resources that this api can retrieve
	 * 
	 * @var Array (series|anime|manga)
	 */
	protected $resources = ['manga'];

	/**
	 * Basic url
	 *
	 * @param string
	 */
	protected $url = "http://mangafox.me/";

	/**
	 * Discovery a resource
	 *
	 * @param string $keys
	 */
	public function discovery($key){

		return $this -> all($key);
	}

	/**
	 * Perform the request to the api in order to discovery new series
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function all($key){

		
		$name_request = $this -> getName()."_search_".$key;

		if($cache = Cache::get($name_request))
			return $cache;
			

		$client = new Client();
		

		$params = [
			'name_method' => 'bw',
			'name' => str_replace("%20"," ",$key),
			'type' => '',
			'author_method' => 'cw',
			'author' => '',
			'artist_method' => 'cw',
			'artist' => '',
			'realesed_method' => 'eq',
			'realesed' => '',
			'rating_method' => 'eq',
			'rating' => '',
			'is_completed' => '',
			'advopts' => 1
		];

		$response = $client -> request($this -> url."search.php",'GET',$params);


		$collection = CollectionObject::create($response);

		foreach($collection as $n => $row){

			$poster = explode("?",$row['poster'])[0];
			$basename = basename($poster);
			$destination = 'uploads/manga-fox/'.$row['id'].".jpg";

			if(!file_exists(dirname($destination))){
				mkdir(dirname($destination),0777,true);
			}
			
			if(!file_exists($destination)){	
				$client -> download($poster,$destination);
			}

			$collection[$n]['poster'] = Request::getDirUrl()."/".$destination;
				
		}


		Cache::set($name_request,$collection,3600);

		return $collection;
	}

	/**
	 * Get a resource
	 * 
	 * @param int $id
	 *
	 * @return response
	 */
	public function get($id){
		$client = new Client();

		# 
		$response = $client -> request($this -> url."manga/{$id}/");

		#
		$manga = MangaObject::long($response);

		# Retrieve banner
		$poster = explode("?",$manga -> poster)[0];
		$basename = basename($poster);
		$destination = 'uploads/manga-fox/'.$manga -> id.".jpg";

		if(!file_exists(dirname($destination))){
			mkdir(dirname($destination),0777,true);
		}
		
		if(!file_exists($destination)){	
			$client -> download($poster,$destination);
		}

		$manga -> poster = $poster;
		$manga -> type = 'manga';

		# It's time to retrieve all scan, or maybe not

		return $manga;
	}

	public function update(){
		
	}

}