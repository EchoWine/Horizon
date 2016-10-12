<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;

class DashboardController extends Controller{


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
		$router -> any('admin','dashboard') -> as('admin/dashboard');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function dashboard(){
		return $this -> view('WT/admin/dashboard');
	}
}

?>