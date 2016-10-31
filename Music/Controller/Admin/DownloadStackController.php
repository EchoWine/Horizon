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
			$view -> id();
			$view -> username();
			$view -> email();
		});

		$views -> add(function($view){
			$view -> username();
			$view -> email();
			$view -> token();
			$view -> password();

		});

		$views -> edit(function($view){
			$view -> username();
			$view -> email();
			$view -> token();
			$view -> password();
		});

		$views -> get(function($view){
			$view -> id();
			$view -> username();
			$view -> email();
			$view -> token();

		});

		$views -> search(function($view){
			$view -> id();
			$view -> username();
			$view -> email();
			$view -> token();

		});
	}

}

?>