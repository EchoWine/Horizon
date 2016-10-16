<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;
use WT\Service\WT;

class ResourceController extends Controller{

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

		$router -> any('admin/resource/{resource_type}/{resource_id}','get') -> as('admin.resource');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function get($request,$resource_type,$resource_id){

		$resource_class = WT::getClassByType($resource_type);
		$resource = $resource_class::where('id',$resource_id) -> first();

		return $this -> view('WT/admin/resource_complete',['resource' => $resource]);
	}
}

?>