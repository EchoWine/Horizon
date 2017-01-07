<?php

namespace Admin\View\Form;

class Date extends Base{


	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct($name){
		$this -> name = $name;
		$this -> value = new \DateTime();
	}
}