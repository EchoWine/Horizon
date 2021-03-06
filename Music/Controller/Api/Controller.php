<?php

namespace Music\Controller\Api;

use CoreWine\Http\Controller as HttpController;

use CoreWine\Youtube\Youtube;

use Music\Model\Playlist;
use Music\Model\PlaylistSync;
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
			
			$raw_url = $request -> request -> get('youtube_url');

			$d = DownloadStack::firstOrCreate([
				'playlist_id' => $playlist -> id,
				'url' => $raw_url,
				'user_id' => $user -> id
			]);

			return $d 
				? $this -> success('Added successfully')
				: $this -> error('Something goes wrong');

		}catch(\Exception $e){
			return $this -> error($e -> __toString());
		}
	}

	/**
	 * @POST
	 * 
	 * Add a new sync playlist
	 *
	 * @param integer $id
	 */
	public function addSyncPlaylist($request,$id){
		try{

			if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
				return $this -> error('Token invalid');

			$playlist = Playlist::where('id',$id) -> first();

			if(!$playlist && $playlist -> user != $user)
				return $this -> error('Cannot find playlist');
			
			$raw_url = $request -> request -> get('youtube_url');

			$d = PlaylistSync::firstOrCreate([
				'playlist_id' => $playlist -> id,
				'url' => $raw_url,
			]);

			return $d 
				? $this -> success('Added successfully')
				: $this -> error('Something goes wrong');

		}catch(\Exception $e){
			return $this -> error($e -> __toString());
		}
	}

	/**
	 * @POST
	 * 
	 * Remove sync playlist
	 *
	 * @param integer $id
	 */
	public function removeSyncPlaylist($request,$id,$sync_id){

		try{

			if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
				return $this -> error('Token invalid');

			$playlist = Playlist::where('id',$id) -> first();

			if(!$playlist && $playlist -> user != $user)
				return $this -> error('Cannot find playlist');

			$playlist_sync = PlaylistSync::where('id',$sync_id) -> first();

			if(!$playlist_sync)
				return $this -> error('Cannot find playlist');
			
			$playlist_sync -> delete();

			return $d 
				? $this -> success('Removed successfully')
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