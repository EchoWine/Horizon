<?php

namespace Admin\Controller;

use CoreWine\Http\Router;
use CoreWine\Http\Request as Request;
use Auth\Service\Auth;
use Admin\View\Views;
use Api\Controller;
use Admin\Item;

abstract class AdminController extends Controller{

	/**
	 * Prefix route
	 *
	 * @var string
	 */
	const PREFIX_ROUTE = 'admin/';

	/**
	 * Prefix route
	 *
	 * @var string
	 */
	public $url_alias;

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

		$router -> any(static::PREFIX_ROUTE.$this -> url,'index') -> as($this -> url_alias);
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
	public function index(Request $request){
		$views = new Views($this -> getSchema(),$this -> getApiUrl());

		$this -> views($views);

		$item = new Item();
		$item -> setUrl($this -> getFullApiURL());
		$item -> setViews($views);
		$item -> setName($this -> url);

		return $this -> view($this -> view,['item' => $item]);
	}


}
?>