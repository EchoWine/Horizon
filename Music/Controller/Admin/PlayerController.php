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
		return $this -> view('Music/admin/player',[]);
	}

}

?>