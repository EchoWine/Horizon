<?php

namespace WT\Controller\Api;

use CoreWine\Http\Controller as Controller;
use WT\Service\WT;
use Auth;

class SearchController extends Controller{


	/**
	 * Define your routes
	 *
	 * @param Router $router
	 */
	public function __routes($router){
		
		# Discovery and add new resource
		$router -> get('/api/v1/discovery/{database}/{key}','discovery','wt.discovery.get');
		$router -> post('/api/v1/discovery/{database}/{id}','add','wt.discovery.add');

		# Manipulate/Retrieve resoure
		$router -> get('/api/v1/{resource}','all','wt.resource.all');
		$router -> get('/api/v1/{resource}/{id}','get','wt.resource.get');
		$router -> post('/api/v1/{resource}/update/{id}','sync','wt.resource.update');
		$router -> delete('/api/v1/{resource}/{id}','remove','wt.resource.delete');


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
	public function discovery($request,$database,$key){

		if(!($user = Auth::getUserByToken($request -> query -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		

		return $this -> json(WT::discovery($user,$database,$key));
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
	public function add($request,$database,$id){

		if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		
		
		return $this -> json(WT::add(
			$user,
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
	public function all($request,$database){
		
		if(!($user = Auth::getUserByToken($request -> query -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		
		
		return $this -> json(WT::all(
			$user,
			$database
		));

	}

	/**
	 * @GET
	 *
	 * @param Request $request
	 * @param string $database
	 * @param integer $id
	 *
	 * @return Response
	 */
	public function get($request,$resource,$id){
		
		if(!($user = Auth::getUserByToken($request -> query -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		
		
		return $this -> json(WT::get(
			$user,
			$resource,
			$id
		));
	}

	/**
	 * @POST
	 *
	 * @param Request $request
	 * @param string $database
	 * @param integer $id
	 *
	 * @return Response
	 */
	public function sync($request,$resource,$id){
		
		if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		
		
		return $this -> json(WT::sync(
			$user,
			$resource,
			$id
		));

	}

	/**
	 * @DELETE
	 *
	 * @param Request $request
	 * @param string $database
	 * @param integer $id
	 *
	 * @return Response
	 */
	public function remove($request,$resource,$id){

		if(!($user = Auth::getUserByToken($request -> request -> get('token'))))
			return $this -> json(['status' => 'error','message' => 'Token invalid']);
		
		
		return $this -> json(WT::delete(
			$user,
			$resource,
			$id
		));
	}


}