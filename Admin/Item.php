<?php

namespace Admin;

use Admin\View\Views;

class Item{

	/**
	 * Name
	 *
	 * @var name
	 */
	protected $name;

	/**
	 * Views
	 *
	 * @var Views
	 */
	protected $views;

	/**
	 * Url
	 *
	 * @var string
	 */
	protected $url;	
	
	/**
	 * Construct
	 *
	 */
	public function __construct(){}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName($name){
		$this -> name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName(){
		return $this -> name;
	}

	/**
	 * Set views
	 *
	 * @param Views $views
	 *
	 * @return void
	 */
	public function setViews($views){
		$this -> views = $views;
	}

	/**
	 * Get views
	 *
	 * @return Views
	 */
	public function getViews(){
		return $this -> views;
	}

	/**
	 * Set url
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function setUrl($url){
		$this -> url = $url;
	}

	/**
	 * Get url
	 *
	 * @return string
	 */
	public function getUrl(){
		return $this -> url;
	}

	/**
	 * Generate views
	 *
	 * @param string $name
	 * @param string $model
	 * @param closure $closure
	 * 
	 * @return void
	 */
	public function generateViews($name,$model,$closure){
		$views = new Views($model::schema(),$name);

		$closure($views);

		$this -> setViews($views);
	}

	
}
?>