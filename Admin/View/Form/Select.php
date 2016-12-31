<?php

namespace Admin\View\Form;

class Select extends Base{


	/**
	 * Options
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Set options
	 *
	 * @param array $options
	 *
	 * @return this
	 */
	public function options($options){
		$this -> options = $options;
		return $this;
	}

	/**
	 * Get options
	 *
	 * @return array
	 */
	public function getOptions(){
		return $this -> options;
	}

}