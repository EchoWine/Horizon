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

		$router -> any('admin/wt/search','search') -> as('wt:admin.search');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function search(){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_BASIC))
			return abort(404);
		
		return $this -> view('WT/admin/search',[]);
	}
}

?>