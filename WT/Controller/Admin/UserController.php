<?php

namespace WT\Controller\Admin;

use Auth\Model\User;

use CoreWine\Http\Request;

class UserController extends AdminController{

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Auth\Model\User';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'users';

	/**
	 * Set views
	 *
	 * @param  $views
	 */
	public function views($views){
		
		$views -> all(function($view){
			$view -> id();
			$view -> username();
			$view -> email();
		});

		$views -> add(function($view){
			$view -> username();
			$view -> email();
			$view -> token();
			$view -> password();
			$view -> permission() -> selectMultiple(User::getPermission());

		});

		$views -> edit(function($view){
			$view -> username();
			$view -> email();
			$view -> token();
			$view -> password();
			$view -> permission() -> selectMultiple(User::getPermission());
		});

		$views -> get(function($view){
			$view -> id();
			$view -> username();
			$view -> email();
			$view -> token();
			$view -> permission() -> selectMultiple(User::getPermission());

		});

		$views -> search(function($view){
			$view -> id();
			$view -> username();
			$view -> email();
			$view -> token();

		});
	}


	/**
	 * Index
	 *
	 * @return Response
	 */
	public function index(Request $request){
		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_USERS))
			return abort(404);
		

		return parent::index($request);
	}

}

?>