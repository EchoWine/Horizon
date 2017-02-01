<?php

namespace Work\Controller\Admin;

use CoreWine\Http\Request;
use CoreWine\Http\Controller;
use Api\Response;
use Work\Model\Customer;

class InvoiceCustomerController extends Controller{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Defining routes
	 */
	public function __routes($router){

		$router -> get("/api/v1/crud/wk_invoice_customers",'all');

	}

	/**
	 * @GET
	 *
	 * @return Response
	 */
	public function all(Request $request){

		$model = Customer::class;

		$repository = $model::repository();

		if($request -> query -> get('id'))
			$repository = $repository -> where('id',$request -> query -> get('id'));
		else
			$repository = $repository -> whereLike("CONCAT('#',id,' - ',fullname)",'%'.$request -> query -> get('filter','').'%');

		$results = $repository 
			-> where('user_id',\Auth::user() -> id)
			-> get();

		$results = $results -> map(function($value,$key){
			return ['id' => $value -> id,'value' => "#".$value -> id." - ".$value -> fullname];
		});

		return new Response\ApiAllSuccess([
			'results' => $results -> toArray(),
		]);
	}
}

?>