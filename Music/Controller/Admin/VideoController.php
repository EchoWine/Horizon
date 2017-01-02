<?php

namespace Music\Controller\Admin;

use CoreWine\Http\Controller as HttpController;

use CoreWine\Http\Request;

use Music\Model\Video;

use Admin\Item;

class VideoController extends HttpController{

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
		$router -> any('admin/music/video/{id}','video') -> as('music:video');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function video(Request $request,$id){

		$video = Video::where('id',$id) -> get();
		

	}


}

?>