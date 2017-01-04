<?php

namespace WT\Controller\Api;

use CoreWine\Http\Controller as Controller;
use WT\Service\WT;
use Auth;
use CoreWine\Http\Request;

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
		
		# Discovery and add new resource
		$router -> get('/api/v1/wt/discovery/{database}/{key}','discovery','wt.discovery.get');
		$router -> post('/api/v1/wt/discovery/{database}/{id}','add','wt.discovery.add');

		# Manipulate/Retrieve resoure
		$router -> get('/api/v1/wt/{resource}','all','wt.resource.all');
		$router -> get('/api/v1/wt/{resource}/{id}','get','wt.resource.get');
		$router -> post('/api/v1/wt/{resource}/update/{id}','sync','wt.resource.update');
		$router -> delete('/api/v1/wt/{resource}/{id}','remove','wt.resource.delete');
		$router -> post('/api/v1/wt/resource/consume/{id}','consume','wt.resource.delete');


		//$router -> get("/api/v1/{resource}/discovery/{key}","index")

	}

	/**
	 * @GET
	 *
	 * @param Request $request
	 * @param string $database
	 * @param string $key
	 * 	 
	 * @return Response
	 */
	public function discovery(Request $request,$database,$key){

		return $this -> json(WT::discovery(Auth::user(),$database,$key));
	}

	/**
	 * @POST
	 *
	 * @param Request $request
	 * @param string $database
	 * @param int $id
	 *
	 * @return Response
	 */
	public function add(Request $request,$database,$id){
		
		return $this -> json(WT::add(
			Auth::user(),
			$database,
			$id
		));
	}
	
	/**
	 * @GET
	 *
	 * @param Request $request
	 * @param string $database
	 *
	 * @return Response
	 */
	public function all(Request $request,$database){
		
		return $this -> json(WT::all(
			Auth::user(),
			$database,
			[
				'sort' => $request -> query -> get('sort','name'), 
			]
		));

	}

	/**
	 * @GET
	 *
	 * @param Request $request
	 * @param string $resource_type
	 * @param integer $resource_id
	 *
	 * @return Response
	 */
	public function get(Request $request,$resource_type,$resource_id){
		
		return $this -> json(WT::get(
			Auth::user(),
			$resource_type,
			$resource_id
		));

	}

	/**
	 * @POST
	 *
	 * @param Request $request
	 * @param string $resource_type
	 * @param integer $resource_id
	 *
	 * @return Response
	 */
	public function sync(Request $request,$resource_type,$resource_id){
		
		return $this -> json(WT::sync(
			Auth::user(),
			$resource_type,
			$resource_id
		));

	}

	/**
	 * @DELETE
	 *
	 * @param Request $request
	 * @param string $resource_type
	 * @param integer $resource_id
	 *
	 * @return Response
	 */
	public function remove(Request $request,$resource_type,$resource_id){
		
		return $this -> json(WT::delete(
			Auth::user(),
			$resource_type,
			$resource_id
		));
	}

	/**
	 * @POST
	 *
	 * @param Request $request
	 * @param integer $container_id
	 *
	 * @return Response
	 */
	public function consume(Request $request,$container_id){
		
		return $this -> json(WT::consume(
			Auth::user(),
			$container_id,
			$request -> request -> get('consume')
		));

	}

}