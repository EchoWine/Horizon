<?php

namespace Music\Controller\Admin;

class DownloadStackController extends AdminController{

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Music\Model\DownloadStack';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'download_stack';

	/**
	 * Set views
	 *
	 * @param  $views
	 */
	public function views($views){
		
		$views -> all(function($view){
			$view -> id('id');
			$view -> text('username');
			$view -> email('email');
		});

		$views -> add(function($view){
			$view -> text('username');
			$view -> email('email');
			$view -> text('token');
			$view -> password('password');
		});

		$views -> edit(function($view){
			$view -> text('username');
			$view -> email('email');
			$view -> text('token');
			$view -> password('password');
		});

		$views -> get(function($view){
			$view -> id('id');
			$view -> text('username');
			$view -> email('email');
			$view -> text('token');
		});

		$views -> search(function($view){
			$view -> id('id');
			$view -> text('username');
			$view -> email('email');
		});
	}

}

?>