<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Controller as BasicController;


class ToolsController extends BasicController{

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

		$router -> any('admin/tools','tools');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function tools(){

		return $this -> view('WT/admin/tools',[]);
	}
}

?>