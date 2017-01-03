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

		$router -> any('admin/wt/tools','tools') -> as('wt:admin.tools');
	}

	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function tools(){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_ADVANCED))
			return abort(404);
		

		return $this -> view('WT/admin/tools',[]);
	}
}

?>