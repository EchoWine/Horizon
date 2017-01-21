<?php

namespace Work\Controller\Admin;

use Admin\Controller\AdminController as AdminBasicController;

abstract class AdminController extends AdminBasicController{

	public $view = 'WT/admin/item';

	/**
	 * Prefix route
	 *
	 * @var string
	 */
	const PREFIX_ROUTE = '';
}

?>