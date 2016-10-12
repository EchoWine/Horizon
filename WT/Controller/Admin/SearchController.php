<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Controller as BasicController;


class SearchController extends BasicController{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Define your routes
	 *
	 * @param Router $router
	 */
	public function __routes($router){

		$router -> any('admin/search','search');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function search(){

		return $this -> view('WT/admin/search',[]);
	}
}

?>