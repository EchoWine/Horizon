<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;

class IndexController extends Controller{

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

		$router -> any('/','index') -> as('index');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function index(){
		
		return $this -> redirect('admin/dashboard');
	}
}

?>