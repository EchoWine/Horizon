<?php

namespace Music\Controller\Api;

use CoreWine\Http\Controller as HttpController;

use CoreWine\Youtube\Youtube;

use Music\Model\Playlist;
use Music\Model\DownloadStack;
use Music\Model\Video;

use Auth\Service\Auth;

class Controller extends HttpController{

	/**
	 * Routes
	 *
	 * @param $router
	 */
	public function __routes($router){
		$router -> post('/api/v1/music/playlist/{id}/video/retrieve','retrieveVideoToPlaylist','music.playlist.retrieve');
	}

	/**
	 * @POST
	 *
	 * @param integer $id
	 */
	public function retrieveVideoToPlaylist($request,$id){
		try{


			if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
				return $this -> error('Token invalid');

			$playlist = Playlist::where('id',$id) -> first();

			if(!$playlist && $playlist -> user != $user)
				return $this -> error('Cannot find playlist');
			

			$url = $request -> request -> get('youtube_url');

			# Extract ?v= from $url;
			$url = parse_url($url);

			if(!isset($url['query']))
				return $this -> error('Incorrect URL');

			parse_str($url['query'],$query);

			if(!isset($query['v']))
				return $this -> error('Incorrect URL');

			$yt = Youtube::video($query['v']);
			$title = $yt -> getTitle();
			$url = $yt -> getVideoCloserTo('720p') -> getUrl();

			$d = DownloadStack::firstOrCreate(['playlist_id' => $playlist -> id,'name' => $title,'url' => $url,'user_id' => $user -> id]);

			return $d 
				? $this -> success('Added successfully')
				: $this -> error('Something goes wrong');

		}catch(\Exception $e){
			return $this -> error($e -> __toString());
		}
	}

	/**
	 * Return a JSON success response
	 *
	 * @param string $message
	 *
	 * @return Response
	 */
	public function success($message){
		return $this -> json(['status' => 'success','message' => $message]);
	}

	/**
	 * Return a JSON error response
	 *
	 * @param string $message
	 *
	 * @return Response
	 */
	public function error($message){
		return $this -> json(['status' => 'error','message' => $message]);
	}

}