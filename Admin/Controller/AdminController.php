<?php

namespace Admin\Controller;

use CoreWine\Http\Router;
use CoreWine\Http\Request as Request;
use Auth\Service\Auth;
use Admin\View\Views;
use Api\Controller;

abstract class AdminController extends Controller{

	/**
	 * Prefix route
	 *
	 * @var string
	 */
	const PREFIX_ROUTE = 'admin/';

	/**
	 * View of item
	 *
	 * @var string
	 */
	public $view = 'Admin/admin/item';

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

		parent::__routes($router);

		$router -> any(AdminController::PREFIX_ROUTE.$this -> url,'index');
	}

	/**
	 * Set views
	 */
	public function views($views){}

	/**
	 * Index
	 *
	 * @return Response
	 */
	public function index(){
		$views = new Views($this -> getSchema(),$this -> getApiUrl());

		$this -> views($views);

		return $this -> view($this -> view,[
			'table' => $this -> url,
			'api_url' => $this -> getApiUrl(),
			'api' => $this -> getFullApiURL(),
			'views' => $views,
			'sort_by_field' => $this -> getSchema() -> getSortDefaultField() -> getName(),
			'sort_by_direction' => $this -> getSchema() -> getSortDefaultDirection(),
		]);
	}


}
?>