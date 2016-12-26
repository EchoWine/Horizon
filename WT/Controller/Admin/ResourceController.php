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
		$router -> any('admin/resource/manga/{manga_id}/chapter/{chapter_id}/next','chapterNext') -> as('admin.chapter.next');

	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function get($request,$resource_type,$resource_id){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_BASIC))
			return abort(404);

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

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_BASIC))
			return abort(404);

		$chapter = Chapter::where('id',$chapter_id) -> first();
		$resource = $chapter -> manga;

		return $this -> view('WT/admin/chapter',['chapter' => $chapter,'resource' => $resource]);
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function chapterNext($request,$manga_id,$chapter_id){

		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_WT_BASIC))
			return abort(404);

		$chapter = Chapter::where('id',$chapter_id) -> first();
		$manga = $chapter -> manga;

		$chapter_next = Chapter::where('manga_id',$manga -> id) -> where('number','>',$chapter -> number) -> orderByAsc('number') -> first();

		return $chapter_next 
			? redirect(route('admin.chapter',['manga_id' => $manga -> id,'chapter_id' => $chapter_next -> id]))
			: redirect(route('admin.resource',['resource_type' => $manga -> container -> type,'resource_id' => $manga -> id]));

	}
}

?>