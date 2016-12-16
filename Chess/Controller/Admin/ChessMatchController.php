<?php

namespace Chess\Controller\Admin;

class ChessMatchController extends AdminController{

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Chess\Model\ChessMatch';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'chess_matches';

	/**
	 * Set views
	 *
	 * @param  $views
	 */
	public function views($views){
		
		$views -> all(function($view){
			$view -> id() -> hidden();
			$view -> player();
			$view -> opponent();
			$view -> date();
			$view -> score();
			$view -> description();
		});

		$views -> add(function($view){
			$view -> player();
			$view -> opponent();
			$view -> date();
			$view -> score();
			$view -> description();
			$view -> url();
		});

		$views -> edit(function($view){
			$view -> player();
			$view -> opponent();
			$view -> date();
			$view -> score();
			$view -> description();
			$view -> url();
		});

		$views -> get(function($view){
			$view -> player();
			$view -> opponent();
			$view -> date();
			$view -> score();
			$view -> description();
			$view -> url();

		});

		$views -> search(function($view){
			$view -> id();
			$view -> player();
			$view -> opponent();
			$view -> date();
			$view -> description();
			$view -> score();
		});
	}

}

?>