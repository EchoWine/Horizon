<?php

namespace Admin\View\Component;

class SelectMultiple{

	public $options;

	public function __construct($options){
		$this -> options = $options;
	}

	public function getOptions(){
		return $this -> options;
	}
}
?>