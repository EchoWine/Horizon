<?php

namespace WT\Api;

use CoreWine\Component\Str;
use CoreWine\Http\Client;
use CoreWine\Component\File;
use CoreWine\Component\DomDocument;
use CoreWine\Http\Request;
use Cache;
use WT\Api\MangaFox\MangaObject;
use WT\Api\MangaFox\ScanObject;
use WT\Api\MangaFox\CollectionObject;
use Cfg;

use WT\Model\Queue\Chapter as QueueChapter;

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


			$collection[$n]['poster'] = $this -> download($row['poster'],'manga-fox/'.$row['id'].".jpg");


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
			
		$manga -> type = 'manga';

		return $manga;

		# It's time to retrieve all scan, or maybe not

		return $manga;
	}

	public function update(){
		$client = new Client();
		
		$response = $client -> request($this -> url."releases/");

		return CollectionObject::releases($response);

	}

	public function queueDownload($limit){

		# Download chapters
		$queue = QueueChapter::take($limit) -> orderBy('id','DESC') -> get();

		$client = new Client();

		foreach($queue as $queue_chapter){
			$chapter = $queue_chapter -> chapter;


			# Download all scans

			$first = $chapter -> scan;
			//http://m.mangafox.me/manga/one_piece/vTBD/c850/1.html

			$chapter_n = basename(dirname($first)); #c850

			# Clear all files
			$chapter -> raw() -> clear();

			$next = $first;


			do{
				try{
					$response = $client -> request($next,'GET',[]);

					$scan = ScanObject::create($response);

					$scan -> next = dirname($first)."/".$scan -> next;

					$next = $scan -> next;

					$chapter -> raw() -> addByUrl($scan -> raw);
					$chapter -> save();

					# Stop if next isn't current chapter
					$chapter_next = basename(dirname($next));


					if($chapter_next !== $chapter_n)
						$next = null;


				}catch(\Exception $e){
					$next = null;
				}

			}while($next);
		

			$queue_chapter -> delete();
		}

	}

}