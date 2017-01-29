<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;
use CoreWine\Http\Response\JSONResponse;

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
		$router -> any('admin/wt/export','libraryExport') -> as('wt:admin.library.export');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function library(){

		return $this -> view('WT/admin/library');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function libraryExport(){

		$filename = 'wt-horizon-exports.json';

		$collection = \WT\Service\WT::all(Auth::user(),'all',['filter' => '','sort_field' => 'name','sort_direction' => 'asc']);

		$collection = $collection -> map(function($value){
			return ['type' => $value['type'],'name' => $value['name']];
		});

		$response = new JSONResponse();
		$response -> setBody($collection);
		$response -> header('Content-Type','application/json');
		$response -> header('Content-Disposition'," attachment; filename='{$filename}'");
		return $response;
	}
}

?>