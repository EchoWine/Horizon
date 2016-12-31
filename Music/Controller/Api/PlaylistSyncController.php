<?php

namespace Music\Controller\Api;

use CoreWine\Http\Request;

use Api\Controller as ApiController;

class PlaylistSyncController extends ApiController{

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Music\Model\PlaylistSync';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'playlist_sync';

	/**
	 * Middleware
	 *
	 * @var Array
	 */
	public $middleware = ['Admin\Middleware\Authenticate'];

}

?>