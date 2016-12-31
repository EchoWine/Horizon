<?php

namespace Admin\View;

use Admin\View\Exceptions as Exceptions;

class ViewBuilder{

	/**
	 * Schema
	 *
	 * @var ORM\Schema
	 */
	public $schema;
	
	/**
	 * Form
	 *
	 * @var Admin\View\Form\Base
	 */
	public $form;


	/**
	 * Get schema
	 *
	 * @return ORM\Schema
	 */
	public function getSchema(){
		return $this -> schema;
	}

	/**
	 * Get schema
	 *
	 * @return ORM\Schema
	 */
	public function getForm(){
		return $this -> form;
	}

	/**
	 * Construct
	 */
	public function __construct($schema,$form){
		$this -> schema = $schema;
		$this -> form = $form;

	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName(){
		return $this -> getSchema() -> getName();
	}

	/**
	 * Get column
	 *
	 * @return string
	 */
	public function getColumn(){
		return $this -> getSchema() -> getColumn();
	}

	/**
	 * Get alias
	 *
	 * @return string
	 */
	public function getAlias(){
		return $this -> getForm() -> getAlias();
	}

	/**
	 * Is alias
	 *
	 * @param string $alias
	 *
	 * @return bool
	 */
	public function is($alias){
		return $this -> getForm() -> is($alias);
	}


}
?>