<?php

namespace Music\Controller\Admin;

use CoreWine\Http\Controller as HttpController;

class PlayerController extends HttpController{

	/**
	 * Routes
	 *
	 * @param $router
	 */
	public function __routes($router){
		$router -> any('admin/music/player','player');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function player($request){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_MUSIC))
			return abort(404);

		return $this -> view('Music/admin/player',[]);
	}

}

?>