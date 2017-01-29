<?php

namespace Admin\Controller;

use CoreWine\Http\Request;
use CoreWine\Http\Router;
use CoreWine\Http\Flash;
use CoreWine\Component\Cfg;

use Auth\Service\Auth;
use Auth\Repository\AuthRepository;
use CoreWine\Http\Controller as Controller;

class LogoutController extends Controller{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Routers
	 */
	public function __routes($router){

		$router -> get('admin/logout','logout') -> as('auth.logout');
	}

	/**
	 * @GET
	 *
	 * @return Response
	 */
	public function logout(Request $request){

		Auth::logout(Auth::user());

		Request::redirect(Router::url('auth.form.login'));
	}
}

?>