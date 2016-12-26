<?php

namespace Music\Controller\Admin;
use Auth\Service\Auth;

use CoreWine\Http\Request;

class PlaylistsController extends AdminController{

	public $view = 'Music/admin/item-playlist';

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Music\Model\Playlist';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'playlists';

	/**
	 * Set views
	 *
	 * @param  $views
	 */
	public function views($views){
		
		$views -> all(function($view){
			$view -> id();
			$view -> name();
		});

		$views -> add(function($view){
			$view -> name();

		});

		$views -> edit(function($view){
			$view -> name();
		});

		$views -> get(function($view){
			$view -> id();
			$view -> name();

		});

		$views -> search(function($view){
			$view -> id();
			$view -> name();

		});
	}

	// Add user automatically when new entity is created..
	public function __insert($playlist){

		$playlist -> user = Auth::user(); 
	}

	/**
	 * Index
	 *
	 * @return Response
	 */
	public function index(Request $request){
		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_MUSIC))
			return abort(404);
		

		return parent::index($request);
	}


}

?>