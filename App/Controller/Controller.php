<?php

namespace App\Controller;

use CoreWine\Http\Request;
use CoreWine\Http\Router;

use CoreWine\Http\Controller as BaseController;

class Controller extends BaseController{

	
	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];



}

?>