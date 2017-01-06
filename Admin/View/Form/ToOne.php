<?php

namespace Admin\View\Form;

class ToOne extends Base{


	/**
	 * urls
	 *
	 * @var array
	 */
	public $url = [];


	/**
	 * Set single url
	 *
	 * @param string $url
	 *
	 * @return this
	 */
	public function url($url){

		$this -> url = $url;

		return $this;
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
	 * Resolver
	 *
	 * @param string $field
	 * @param string $match
	 *
	 * @return $this
	 * 
	 */
	public function column($column){
		$this -> column = $column;

		return $this;
	}

}