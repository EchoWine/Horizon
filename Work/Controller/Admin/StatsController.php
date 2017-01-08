<?php

namespace Work\Controller\Admin;

use CoreWine\Http\Request;
use CoreWine\Http\Controller as Controller;
use Auth\Service\Auth;

use Work\Model\Invoice;
use Work\Collection\InvoiceCollection;


class StatsController extends Controller{

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

		$router -> any('admin/work/stats','stats') -> as('work:admin.stats');
	}
	
	/**
	 * @ANY
	 *
	 * @return Response
	 */
	public function stats(Request $request){

		$year = $request -> query -> get('year',date('Y'));

		$invoices = Invoice::where(['year' => $year,'user_id' => Auth::user() -> id]) -> all();
		$invoices = new InvoiceCollection($invoices);

		return $this -> view('Work/admin/stats',[
			'invoices' => $invoices,
			'year' => $year
		]);
	}
}

?>