<?php

namespace Admin\Middleware;

use CoreWine\Http\Request;
use CoreWine\Http\Router;
use CoreWine\Http\Middleware;
use Auth\Service\Auth;

class Authenticate extends Middleware{

	/**
	 * Handle
	 */
	public function handle(Request $request){

		# Remove session expire
		Auth::load();

		# Authenticate User
		Auth::authenticate($request -> session -> get('session'));
		Auth::authenticate($request -> cookie -> get('session'));
		Auth::authenticate($request -> request -> get('token'));
		Auth::authenticate($request -> query -> get('token'));


		if(!Auth::logged()){
			Request::redirect(Router::url('auth.form.login'));
		}

	}

}

?>