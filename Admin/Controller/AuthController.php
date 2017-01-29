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
	//public $middleware = ['Admin\Middleware\Authenticate'];

	/**
	 * Routers
	 */
	public function __routes($router){

		$router -> get('admin/login','formLogin') -> as('auth.form.login');
		$router -> post('admin/login','login') -> as('auth.login');
	}
	
	/**
	 * @GET
	 *
	 * @return Response
	 */
	public function formLogin(){

		return $this -> view('Admin/auth/login');
	}

	/**
	 * @POST
	 *
	 * @return Response
	 */
	public function login(Request $request){

		if(!Auth::logged()){
			if(!$this -> checkLogin()){
				Request::refresh();
			}
		}



		Request::redirect(Router::url('admin.dashboard'));

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

			$session = Auth::login($users[0],$type['expire']);

			if($type['data'] == 0)
				Request::setCookie(Cfg::get('Auth.cookie'),$session -> sid,$expire);
			else
				Request::setSession(Cfg::get('Auth.cookie'),$session -> sid);


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