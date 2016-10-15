<?php

namespace WT\Api\BakaUpdates;

use WT\Api\Object;
use CoreWine\Component\DomDocument;

class MangaObject extends Object{

	public static function DOMinnerHTML(\DOMNode $element){ 
	    $innerHTML = ""; 
	    $children  = $element->childNodes;

	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }

	    return $innerHTML; 
	} 

	/**
	 * Initialize the object with the response in xml
	 *
	 * @param HTML body $response
	 */
	public static function create($response){


		$dom = new DomDocument($response);
		
		$t = new self();

		# Name
		$t -> name = $dom -> getElementsByAttribute('class','releasestitle tabletitle') -> item(0) -> nodeValue;
		
		# Alias
		$alias = $dom -> getElementsByAttribute('class','sContent') -> item(3);
		$alias = array_diff(explode("<br>",self::DOMinnerHTML($alias)),['']);
		$t -> alias = $alias;

		# Type
		$type =  $dom -> getElementsByAttribute('class','sContent') -> item(1) -> nodeValue;
		$type = str_replace("\n","",$type);
		$t -> type = $type;
		$t -> poster_raw = $dom -> getElementsByAttribute('class','sContainer') -> item(1) -> getElementsByTagName('img') -> item(0) -> getAttribute('src');
		$t -> overview = $dom -> getElementsByAttribute('style','text-align:justify') -> item(0) -> nodeValue;
		
		return $t;

	}

	/**
	 * Retrieve urls of episodes
	 *
	 * @param XML object
	 */
	public static function getUrlsEpisodes($resource){

		$resource;

	}


	/**
	 * Initialize the object with the response in xml
	 *
	 * @param XML object
	 */
	public static function short($resource){
	}


}