<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;

class LibraryController extends Controller{

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

		$router -> any('admin/wt','library') -> as('wt:admin.library');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function library(){

		return $this -> view('WT/admin/library');
	}
}

?>