<?php

namespace Music\Controller\Admin;

use CoreWine\Http\Controller as HttpController;

use CoreWine\Http\Request;

use Music\Model\Playlist;
use Music\Model\PlaylistSync;

use Admin\Item;

class PlayerController extends HttpController{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Routes
	 *
	 * @param $router
	 */
	public function __routes($router){
		$router -> any('admin/music/player/{id}','player') -> as('music.player');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function player(Request $request,$id){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_MUSIC))
			return abort(404);

		$playlist = Playlist::where(['id' => $id,'user_id' => \Auth::user() -> id]) -> first();

		if(!$playlist)
			$playlist = Playlist::where('user_id',\Auth::user() -> id) -> first();
	
		return $this -> view('Music/admin/player',['playlist' => $playlist,'item_playlist_sync' => $this -> getItemPlaylistSync($playlist)]);
	}

	public function getItemPlaylistSync($playlist){

		$item = new Item();
		$item -> setName('playlist_sync');
		$item -> setUrl('/api/v1/crud/playlist_sync');
		$item -> generateViews('playlist_sync',PlaylistSync::class,function($views) use($playlist){

			$views -> all(function($view){
				$view -> id('id');
				$view -> url('url');
			});

			$views -> add(function($view) use($playlist){
				$view -> url('url');
				$view -> hidden('playlist_id') -> value($playlist -> id);
			});

			$views -> edit(function($view) use($playlist){
				$view -> url('url');
				$view -> hidden('playlist_id') -> value($playlist -> id);
			});

			$views -> get(function($view){
				$view -> id('id');
				$view -> url('url');
			});

			$views -> search(function($view){
				$view -> id('id');
				$view -> url('url');
			});
		});

		return $item;


	}

}

?>