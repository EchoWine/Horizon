<?php

namespace Example\Controller;

use CoreWine\Http\Router;

use CoreWine\Http\Controller as Controller;

class ExampleController extends Controller{


	/**
	 * Define your routes
	 *
	 * @param Router $router
	 */
	public function __routes($router){
		$router -> any('/','index');
	}


	/**
	 * Define your routes
	 *
	 * @param Router $router
	 */
	public function index(){
		return $this -> view('Example/index',['yoho' => 'YoHo!!!!!']);
	}
	
}

?>