<?php

namespace Chess\Controller\Admin;

use CoreWine\Http\Request;

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
			$view -> id('id') -> hidden();
			$view -> text('player');
			$view -> text('opponent');
			$view -> dateTime('date');
			$view -> number('score');
			$view -> textarea('description');
		});

		$views -> add(function($view){
			$view -> text('player');
			$view -> text('opponent');
			$view -> dateTime('date');
			$view -> number('score');
			$view -> textarea('description');
			$view -> url('url');
		});

		$views -> edit(function($view){
			$view -> text('player');
			$view -> text('opponent');
			$view -> dateTime('date');
			$view -> number('score');
			$view -> textarea('description');
			$view -> url('url');
		});

		$views -> get(function($view){
			$view -> text('player');
			$view -> text('opponent');
			$view -> dateTime('date');
			$view -> number('score');
			$view -> textarea('description');
			$view -> url('url');
		});
		
		$views -> search(function($view){
			$view -> text('player');
			$view -> text('opponent');
			$view -> dateTime('date');
			$view -> number('score');
			$view -> textarea('description');
			$view -> url('url');
		});
	}


	/**
	 * Index
	 *
	 * @return Response
	 */
	public function index(Request $request){
		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_CHESS))
			return abort(404);
		

		return parent::index($request);
	}
}

?>