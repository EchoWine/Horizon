<?php

namespace WT\Controller\Admin;

use CoreWine\Http\Router;
use CoreWine\Http\Request;
use Auth\Service\Auth;

use CoreWine\Http\Controller as Controller;
use WT\Service\WT;

use WT\Model\Chapter;

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
		$router -> any('admin/resource/manga/{manga_id}/chapter/{chapter_id}','chapter') -> as('admin.chapter');
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
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function chapter($request,$manga_id,$chapter_id){


		$chapter = Chapter::where('id',$chapter_id) -> first();
		$resource = $chapter -> manga;

		return $this -> view('WT/admin/chapter',['chapter' => $chapter,'resource' => $resource]);
	}
}

?>