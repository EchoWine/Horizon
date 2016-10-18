<?php

namespace Admin\Controller;

use CoreWine\Http\Request;
use CoreWine\Http\Router;
use CoreWine\Http\Flash;
use CoreWine\Component\Cfg;

use Auth\Service\Auth;
use Auth\Repository\AuthRepository;
use CoreWine\Http\Controller as Controller;

class AuthController extends Controller{

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	// public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Routers
	 */
	public function __routes($router){

		$router -> get('admin/login','showLogin');
		$router -> post('admin/login','attemptLogin') -> as('admin/login/action');
		$router -> get('admin/logout','attemptLogout');
	}
	
	/**
	 * @GET
	 *
	 * @return Response
	 */
	public function showLogin(){
		Auth::load();
		return $this -> view('Admin/auth/login');
	}

	/**
	 * @POST
	 *
	 * @return Response
	 */
	public function attemptLogin(){

		Auth::load();

		if(!Auth::logged()){
			if(!$this -> checkLogin()){
				Request::refresh();
			}
		}


		Request::redirect(Router::url('admin/dashboard'));

	}

	/**
	 * @GET
	 *
	 * @return Response
	 */
	public function attemptLogout(){

		Auth::load();

		if(Auth::logged()){
			Auth::logout();
		}

		Request::redirect(Router::url('admin/login'));
	}

	/**
	 * Check login
	 */
	public function checkLogin(){

		$user = Request::post('user');
		$pass = Request::post('pass');
		$type = Request::post('remember') !== null;
		$password = Auth::getHashPass($pass);
		$users = Auth::getUsersByRaw($user,$password);

		$type = $type == 1 ? Cfg::get('Auth.remember') : Cfg::get('Auth.normal');

		if(($users_num = count($users)) > 1){
			Flash::add('error','Unable to determine a single user with this data');	

		}else if($users_num == 1){

			Auth::login($users[0],$type);
			return true;
			
		}else{

			if(Cfg::get('Auth.ambiguous')){
				Flash::add('error','The data entered is incorrect');
			}else{

				if($q['user'] !== $user)
					Flash::add('error','Wrong username/email');
				
				if($q['pass'] !== $pass)
					Flash::add('error','Wrong password');
				
			}

		}

		return false;
			
	}


}

?>