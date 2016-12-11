<?php

namespace WT\Api\MangaFox;

use WT\Api\Object;
use CoreWine\Component\DomDocument;
use CoreWine\Component\Collection;
use CoreWine\Http\Client;

class MangaObject extends Object{

	/**
	 * Initialize the object with the response in xml
	 *
	 * @param HTML body $response
	 */
	public static function basic($response){

		$o = new self();

		$td = $response -> getElementsByTagName('td');

		$id = $response -> getElementsByTagName('a') -> item(0) -> getAttribute('rel');
		
		# ID
		$o -> id = $response -> getElementsByTagName('a') -> item(0) -> getAttribute('href');
		$i = explode("/",$o -> id);
		$i = $i[count($i) - 2];
		$o -> id = $i;

		$o -> name = $response -> getElementsByTagName('a') -> item(0) -> nodeValue;



		
		try{
			$client = new Client();
			$response = $client -> request("http://mangafox.me/ajax/series.php",'POST',['sid' => $id]);
			$response = json_decode($response);
			$o -> poster = $response[10];
		}catch(\Exception $e){
		}

		return $o;
	}

	/**
	 * Initialize the object with the response in xml
	 *
	 * @param HTML body $response
	 */
	public static function long($response){

		$o = new self();

		$dom = new DomDocument($response);

		# ID
		$id = $dom -> getElementsByAttribute('property','og:url') -> item(0) -> getAttribute('content');
		$id = basename($id);
		$o -> id = $id;

		# Name
		$keywords = $dom -> getElementsByAttribute('name','keywords') -> item(0) -> getAttribute('content');
		$o -> name = explode(",",$keywords)[0];


		# Overview

		$summary = $dom -> getElementsByAttribute('class','summary') -> item(0);
		$o -> overview = $summary ? $summary -> nodeValue : '';
		
		# Genres
		$genres = $dom -> getElementsByAttribute('valign','top') -> item(3) -> nodeValue;
		$o -> genres = explode(",",$genres);

		# Poster
		$src = $dom -> getElementsByTagName('img') -> item(1) -> getAttribute('src');
		$o -> poster = $src;

		# Banner
		$o -> banner = null;

		# Status
		$status = $dom -> getElementsByAttribute('class','data') -> item(0) -> nodeValue;
		$status = explode(',',$status)[0];
		$status = trim(preg_replace('/\s\s+/', ' ',$status));
		$status = str_replace("Status: ","",$status);
		$o -> status = $status;

		$chapters = new Collection();

		$volumes = $dom -> getElementsByAttribute('class','chlist');

		foreach($volumes as $volume_dom){

			$rows = $volume_dom -> getElementsByTagName('li');

			foreach($rows as $row){
				$c = ChapterObject::create($row);
				$chapters[] = $c;
			}


		}

		$o -> chapters = $chapters -> toArray();

		return $o;
	}

	public static function release($dom){

		$o = new self();

		$a = $dom -> getElementsByTagName('a') -> item(0);

		$href = $a -> getAttribute('href');
		$id = str_replace("http://mangafox.me/manga/","",$href);
		$id = str_replace("/","",$id);

		$o -> id = $id;

		$chapters = new Collection();
		foreach($dom -> getElementsByTagName('dt') as $chapter){
			$chapter = ChapterObject::release($chapter);
			$chapters[] = $chapter;
		}

		$o -> chapters = $chapters;
		return $o;
		
	}

}